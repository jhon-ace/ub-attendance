<?php

namespace App\Livewire\Admin;

use App\Models\Admin\EmployeeAttendanceTimeIn;
use App\Models\Admin\EmployeeAttendanceTimeOut;
use App\Models\Admin\School;
use App\Models\Admin\Department;
use App\Models\Admin\Employee;
use App\Models\Admin\DepartmentWorkingHour;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Carbon\Carbon;
use DateTime;
use DateInterval;
use DateTimeZone;



class ShowEmployeeAttendance extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'employee_id';
    public $sortDirection = 'asc';
    public $selectedSchool = null;
    public $selectedDepartment4 = null;
    public $selectedEmployee = null;
    public $departmentsToShow;
    public $schoolToShow;
    public $departmentToShow;
    public $attendancesToShow;
    public $selectedEmployeeToShow;
    public $startDate = null;
    public $endDate = null;
    public $selectedStartDate = null;
    public $selectedEndDate = null;
    public $selectedAttendanceByDate;


    protected $listeners = ['updateEmployees', 'updateEmployeesByDepartment', 'updateAttendanceByEmployee', 'updateAttendanceByDateRange'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updateDateRange()
    {
        $this->resetPage();
    }
    public function clearSearch()
    {
        $this->search = '';
    }

    public function mount()
    {

        $this->selectedSchool = session('selectedSchool', null);
        $this->selectedDepartment4 = session('selectedDepartment4', null);
        $this->selectedEmployee = session('selectedEmployee', null);
        $this->departmentsToShow = collect([]);
        $this->schoolToShow = collect([]);
        $this->departmentToShow = collect([]);
        $this->attendancesToShow = collect([]);
        $this->selectedEmployeeToShow = collect([]);
        $this->selectedAttendanceByDate = collect([]);
    }

    public function updatingSelectedSchool()
    {
        $this->resetPage();
        $this->updateEmployees();
    }

    public function updatingSelectedDepartment()
    {
        $this->resetPage();
        $this->selectedCourse = null;
        $this->departmentToShow = null;
        $this->selectedEmployee = null;
        $this->selectedEmployeeToShow = null;
        $this->updateEmployeesByDepartment();
        $this->updateAttendanceByEmployee();
        $this->selectedEmployeeToShow = collect([]);
        $this->selectedAttendanceByDate = collect([]);
    }

    public function updatingSelectedEmployee()
    {
        $this->resetPage();
        $this->selectedAttendanceByDate = collect([]); // Reset selectedAttendanceByDate
        $this->startDate = null; // Reset start date
        $this->endDate = null; // Reset end date
        $this->updateAttendanceByEmployee();
    }
    
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }

    public function render()
    {
        // Base query for EmployeeAttendanceTimeIn with left join to EmployeeAttendanceTimeOut
        $queryTimeIn = EmployeeAttendanceTimeIn::query()
            ->with(['employee.school', 'employee.department']);

        $queryTimeOut = EmployeeAttendanceTimeOut::query()
            ->with(['employee.school', 'employee.department']);
            

        // Apply search filters
        // $queryTimeIn = $this->applySearchFilters($queryTimeIn);
        // // $queryTimeOut = $this->applySearchFilters($queryTimeOut);

        // Apply selected school filter
        if ($this->selectedSchool) {
            $queryTimeIn->whereHas('employee', function (Builder $query) {
                $query->where('school_id', $this->selectedSchool);
            });
            $queryTimeOut->whereHas('employee', function (Builder $query) {
                $query->where('school_id', $this->selectedSchool);
            });
            $this->schoolToShow = School::find($this->selectedSchool);
        } else {
            $this->schoolToShow = null;
        }

        // Apply selected department filter
        if ($this->selectedDepartment4) {
            $queryTimeIn->whereHas('employee', function (Builder $query) {
                $query->where('department_id', $this->selectedDepartment4);
            });
            $queryTimeOut->whereHas('employee', function (Builder $query) {
                $query->where('department_id', $this->selectedDepartment4);
            });
            $this->departmentToShow = Department::find($this->selectedDepartment4);
            $employees = Employee::where('department_id', $this->selectedDepartment4)->get();
        } else {
            $this->departmentToShow = null;
            $employees = Employee::all();
        }

        // Apply selected employee filter
        if ($this->selectedEmployee) {
            $queryTimeIn->where('employee_id', $this->selectedEmployee);
            $this->selectedEmployeeToShow = Employee::find($this->selectedEmployee);
            $queryTimeOut->where('employee_id', $this->selectedEmployee);
            $this->selectedEmployeeToShow = Employee::find($this->selectedEmployee);
        } else {
            $this->selectedEmployeeToShow = null;
        }

        // Apply date range filter if both dates are set
        if ($this->startDate && $this->endDate) {
            $queryTimeIn->whereDate('check_in_time', '>=', $this->startDate)
                        ->whereDate('check_in_time', '<=', $this->endDate);

            $queryTimeOut->whereDate('check_out_time', '>=', $this->startDate)
                        ->whereDate('check_out_time', '<=', $this->endDate);
                        
            $selectedAttendanceByDate = $queryTimeIn->get();// Fetch data and assign to selectedAttendanceByDate
            
            $this->selectedAttendanceByDate = $selectedAttendanceByDate;   
        }
        

        $attendanceTimeIn = $queryTimeIn->orderBy($this->sortField, $this->sortDirection)
            ->paginate(500);


        $attendanceTimeOut = $queryTimeOut->orderBy($this->sortField, $this->sortDirection)
            ->paginate(500);


    //       $now = new DateTime('now', new DateTimeZone('Asia/Kuala_Lumpur'));
    //       $missingTimeIn = EmployeeAttendanceTimeIn::whereDate('check_in_time', '=', $now)->get();
    //         $missingTimeOut = EmployeeAttendanceTimeOut::whereNull('check_out_time')->get();
  
    // if(!$missingTimeIn->isEmpty()){
    //       $d ="it has data";
    //     dd($d);

    // }
    // if($missingTimeIn->isEmpty()){
    //       $d ="no data";
    //     dd($d);

    // }



$attendanceData = [];
$overallTotalHours = 0;

foreach ($attendanceTimeIn as $attendance) {
    // Initialize variables for each record
    $hoursWorkedAM = 0;
    $hoursWorkedPM = 0;
    $lateDurationAM = 0;
    $lateDurationPM = 0;
    $undertimeAM = 0;
    $undertimePM = 0;
    $totalHoursLate = 0;
    $totalUndertimeHours = 0;


    $now = new DateTime('now', new DateTimeZone('Asia/Kuala_Lumpur'));
    // Extract date and time from check-in


    // Find corresponding check-out time
    $checkOut = $attendanceTimeOut->where('employee_id', $attendance->employee_id)
                                    ->where('check_out_time', '>=', $attendance->check_in_time)
                                    ->first();

    if ($checkOut) {
        $checkOutDateTime = new DateTime($checkOut->check_out_time);


// $departmentWorkingHour = DepartmentWorkingHour::where('department_id', $attendance->employee->department_id)
//                                                 ->where('day_of_week', '!=', 6)
//                                                 ->first();

        $checkInDateTime = new DateTime($attendance->check_in_time);
        $checkInDate = $checkInDateTime->format('Y-m-d');
        $checkInTime = $checkInDateTime->format('H:i:s'); // Extracted time part
        $dayOfWeek = $checkInDateTime->format('w');

// $departmentWorkingHour = DepartmentWorkingHour::where('department_id', $attendance->employee->department_id)
//                                                 ->where('day_of_week', '!=', 0)
//                                                 ->first();

$departmentWorkingHour = DepartmentWorkingHour::where('department_id', $attendance->employee->department_id)
                                              ->where('day_of_week', $dayOfWeek)
                                              ->where('day_of_week', '!=', 0)
                                              ->first();

    if ($departmentWorkingHour) 
    {   

        
        $mS = $departmentWorkingHour->morning_start_time;
        $morningStartTime = clone $checkInDateTime;
        $morningStartTime->setTime(
            (int) date('H', strtotime($mS)),
            (int) date('i', strtotime($mS)),
            (int) date('s', strtotime($mS))
        );

        $mE = $departmentWorkingHour->morning_end_time;
        $morningEndTime = clone $checkInDateTime;
        $morningEndTime->setTime(
            (int) date('H', strtotime($mE)),
            (int) date('i', strtotime($mE)),
            (int) date('s', strtotime($mE))
        );

        $aS = $departmentWorkingHour->afternoon_start_time;
        $afternoonStartTime = clone $checkInDateTime;
        $afternoonStartTime->setTime(
            (int) date('H', strtotime($aS)),
            (int) date('i', strtotime($aS)),
            (int) date('s', strtotime($aS))
        );

        
       $aE = $departmentWorkingHour->afternoon_end_time;
        $afternoonEndTime = clone $checkInDateTime;
        $afternoonEndTime->setTime(
            (int) date('H', strtotime($aE)),
            (int) date('i', strtotime($aE)),
            (int) date('s', strtotime($aE))
        );

        

        // AM Shift Calculation
        if ($checkInDateTime < $morningEndTime) {
            $effectiveCheckInTime = max($checkInDateTime, $morningStartTime);
            $effectiveCheckOutTime = min($checkOutDateTime, $morningEndTime);
            if ($effectiveCheckInTime < $effectiveCheckOutTime) {
                $intervalAM = $effectiveCheckInTime->diff($effectiveCheckOutTime);
                $hoursWorkedAM = $intervalAM->h + ($intervalAM->i / 60);
                
                // Calculate late duration for AM
                $latestAllowedCheckInAM = clone $morningStartTime;
                $latestAllowedCheckInAM->add(new DateInterval('PT15M'));

                if ($checkInDateTime > $latestAllowedCheckInAM) {

                    $lateIntervalAM = $checkInDateTime->diff($latestAllowedCheckInAM);
                    $lateDurationAM = $lateIntervalAM->h * 60 + $lateIntervalAM->i;
                  
                    
                }

                // // Calculate undertime for AM
                // $scheduledAMMinutes = ($morningStartTime->diff($morningEndTime)->h * 60) + $morningStartTime->diff($morningEndTime)->i;
                // $actualMinutesWorkedAM = ($effectiveCheckOutTime->diff($effectiveCheckInTime)->h * 60) + $effectiveCheckOutTime->diff($effectiveCheckInTime)->i;
                // $undertimeAM = max(0, $scheduledAMMinutes - $actualMinutesWorkedAM);

            }
            // Calculate scheduled AM minutes
                $scheduledAMMinutes = ($morningStartTime->diff($morningEndTime)->h * 60) + $morningStartTime->diff($morningEndTime)->i;

                // Calculate actual minutes worked up to the morning end time
                if ($effectiveCheckOutTime < $morningEndTime) {
                    $actualMinutesUpToEnd = ($effectiveCheckOutTime->diff($morningStartTime)->h * 60) + $effectiveCheckOutTime->diff($morningStartTime)->i;
                } else {
                    $actualMinutesUpToEnd = ($morningEndTime->diff($morningStartTime)->h * 60) + $morningEndTime->diff($morningStartTime)->i;
                }

                // Calculate undertime for AM
                $undertimeAM = max(0, $scheduledAMMinutes - $actualMinutesUpToEnd);


        }

        // PM Shift Calculation
        if ($checkInDateTime < $afternoonEndTime && $checkOutDateTime > $afternoonStartTime) {
            $effectiveCheckInTime = max($checkInDateTime, $afternoonStartTime);
            $effectiveCheckOutTime = min($checkOutDateTime, $afternoonEndTime);
            if ($effectiveCheckInTime < $effectiveCheckOutTime) {
                $intervalPM = $effectiveCheckInTime->diff($effectiveCheckOutTime);
                $hoursWorkedPM = $intervalPM->h + ($intervalPM->i / 60);

                // Calculate late duration for PM
                $latestAllowedCheckInPM = clone $afternoonStartTime;
                $latestAllowedCheckInPM->add(new DateInterval('PT15M'));
                if ($checkInDateTime > $latestAllowedCheckInPM) {
                    $lateIntervalPM = $checkInDateTime->diff($latestAllowedCheckInPM);
                    $lateDurationPM = $lateIntervalPM->h * 60 + $lateIntervalPM->i;
                }

                // // Calculate undertime for PM
                // $scheduledPMMinutes = ($afternoonStartTime->diff($afternoonEndTime)->h * 60) + $afternoonStartTime->diff($afternoonEndTime)->i;
                // $actualMinutesWorkedPM = ($effectiveCheckOutTime->diff($effectiveCheckInTime)->h * 60) + $effectiveCheckOutTime->diff($effectiveCheckInTime)->i;
                // $undertimePM = max(0, $scheduledPMMinutes - $actualMinutesWorkedPM);
            }

            $scheduledPMMinutes = ($afternoonStartTime->diff($afternoonEndTime)->h * 60) + $afternoonStartTime->diff($afternoonEndTime)->i;

            // Calculate actual minutes worked up to the morning end time
            if ($effectiveCheckOutTime < $afternoonEndTime) {
                $actualMinutesUpToEnd = ($effectiveCheckOutTime->diff($afternoonStartTime)->h * 60) + $effectiveCheckOutTime->diff($afternoonStartTime)->i;
            } else {
                $actualMinutesUpToEnd = ($afternoonEndTime->diff($afternoonStartTime)->h * 60) + $afternoonEndTime->diff($afternoonStartTime)->i;
            }

            // Calculate undertime for AM
            $undertimePM = max(0, $scheduledPMMinutes - $actualMinutesUpToEnd);

        }

        // Calculate total hours worked
        $totalHoursWorked = $hoursWorkedAM + $hoursWorkedPM;
        $totalHoursLate = $lateDurationAM + $lateDurationPM;
        $totalUndertimeHours = $undertimeAM + $undertimePM;

        // Determine remark based on lateness
        $remark = ($lateDurationAM > 0 || $lateDurationPM > 0) ? 'Late' : 'Present';

        
        
        // Prepare the key for $attendanceData
        $key = $attendance->employee_id . '-' . $checkInDate;

        // Check if this entry already exists in $attendanceData
        if (isset($attendanceData[$key])) {
            // Update existing entry
            $attendanceData[$key]->hours_workedAM += $hoursWorkedAM;
            $attendanceData[$key]->hours_workedPM += $hoursWorkedPM;
            $attendanceData[$key]->total_hours_worked += $totalHoursWorked;
            $attendanceData[$key]->late_duration += $lateDurationAM;
            $attendanceData[$key]->late_durationPM += $lateDurationPM;
            $attendanceData[$key]->undertimeAM += $undertimeAM;
            $attendanceData[$key]->undertimePM += $undertimePM;
            $attendanceData[$key]->total_late += $totalHoursLate;
            $attendanceData[$key]->remarks = $remark;
            // dd($attendanceData[$key]->undertimeAM += $undertimeAM);
        } else {
            // Create new entry
            $attendanceData[$key] = (object) [
                'employee_id' => $attendance->employee_id,
                'worked_date' => $checkInDate,
                'hours_workedAM' => $hoursWorkedAM,
                'hours_workedPM' => $hoursWorkedPM,
                'total_hours_worked' => $totalHoursWorked,
                'late_duration' => $lateDurationAM,
                'late_durationPM' => $lateDurationPM,
                'undertimeAM' => $undertimeAM,
                'undertimePM' => $undertimePM,
                'total_late' => $totalHoursLate,
                'remarks' => $remark,
                
            ];

            //  session()->put('late_duration', $lateDurationAM);
        }

        // Add total hours worked to overall total
        $overallTotalHours += $totalHoursWorked;
    }
    }
}

// Optionally, you can store the $attendanceData and $overallTotalHours in the session or pass it to a view
session()->put('attendance_data', $attendanceData);

session()->put('overall_total_hours', $overallTotalHours);

// dd($attendanceData);




// $attendanceData = [];
// $overallTotalHours = 0;

// foreach ($attendanceTimeIn as $attendance) {
//     // Initialize variables for each record
//     $hoursWorkedAM = 0;
//     $hoursWorkedPM = 0;
//     $lateDurationAM = 0;
//     $lateDurationPM = 0;
//     $undertimeAM = 0;
//     $undertimePM = 0;

//     // Extract date and time from check-in
//     $checkInDateTime = new DateTime($attendance->check_in_time);
//     $checkInDate = $checkInDateTime->format('Y-m-d');
//     $dayOfWeek = $checkInDateTime->format('w');

//     // Find corresponding check-out time
//     $checkOut = $attendanceTimeOut->where('employee_id', $attendance->employee_id)
//                                     ->where('check_out_time', '>=', $attendance->check_in_time)
//                                     ->first();

//     // Fetch department working hours for the specific day of the week
//     $departmentWorkingHour = DepartmentWorkingHour::where('department_id', $attendance->employee->department_id)
//                                                     ->where('day_of_week', $dayOfWeek)
//                                                     ->first();

 
//         $checkOutDateTime = new DateTime($checkOut->check_out_time);

//         // AM Shift
//         $morningStartTime = new DateTime($departmentWorkingHour->morning_start_time);
//         $morningEndTime = new DateTime($departmentWorkingHour->morning_end_time);
//         if ($checkInDateTime < $morningEndTime) {
//             $effectiveCheckInTime = max($checkInDateTime, $morningStartTime);
//             $effectiveCheckOutTime = min($checkOutDateTime, $morningEndTime);
//             if ($effectiveCheckInTime < $effectiveCheckOutTime) {
//                 $intervalAM = $effectiveCheckInTime->diff($effectiveCheckOutTime);
//                 $hoursWorkedAM = $intervalAM->h + ($intervalAM->i / 60);

//                 // Calculate late duration for AM
//                 $latestAllowedCheckInAM = clone $morningStartTime;
//                 $latestAllowedCheckInAM->add(new DateInterval('PT15M'));
//                 if ($checkInDateTime > $latestAllowedCheckInAM) {
//                     $lateIntervalAM = $checkInDateTime->diff($latestAllowedCheckInAM);
//                     $lateDurationAM = $lateIntervalAM->h * 60 + $lateIntervalAM->i;
//                 }

//                 // Calculate undertime for AM
//                 $scheduledAMMinutes = ($morningStartTime->diff($morningEndTime)->h * 60) + $morningStartTime->diff($morningEndTime)->i;
//                 $actualMinutesWorkedAM = ($effectiveCheckOutTime->diff($effectiveCheckInTime)->h * 60) + $effectiveCheckOutTime->diff($effectiveCheckInTime)->i;
//                 $undertimeAM = max(0, $scheduledAMMinutes - $actualMinutesWorkedAM);
//             }
//         }

//         // PM Shift
//         $afternoonStartTime = new DateTime($departmentWorkingHour->afternoon_start_time);
//         $afternoonEndTime = new DateTime($departmentWorkingHour->afternoon_end_time);
//         if ($checkInDateTime < $afternoonEndTime && $checkOutDateTime > $afternoonStartTime) {
//             $effectiveCheckInTime = max($checkInDateTime, $afternoonStartTime);
//             $effectiveCheckOutTime = min($checkOutDateTime, $afternoonEndTime);
//             if ($effectiveCheckInTime < $effectiveCheckOutTime) {
//                 $intervalPM = $effectiveCheckInTime->diff($effectiveCheckOutTime);
//                 $hoursWorkedPM = $intervalPM->h + ($intervalPM->i / 60);

//                 // Calculate late duration for PM
//                 $latestAllowedCheckInPM = clone $afternoonStartTime;
//                 $latestAllowedCheckInPM->add(new DateInterval('PT15M'));
//                 if ($checkInDateTime > $latestAllowedCheckInPM) {
//                     $lateIntervalPM = $checkInDateTime->diff($latestAllowedCheckInPM);
//                     $lateDurationPM = $lateIntervalPM->h * 60 + $lateIntervalPM->i;
//                 }

//                 // Calculate undertime for PM
//                 $scheduledPMMinutes = ($afternoonStartTime->diff($afternoonEndTime)->h * 60) + $afternoonStartTime->diff($afternoonEndTime)->i;
//                 $actualMinutesWorkedPM = ($effectiveCheckOutTime->diff($effectiveCheckInTime)->h * 60) + $effectiveCheckOutTime->diff($effectiveCheckInTime)->i;
//                 $undertimePM = max(0, $scheduledPMMinutes - $actualMinutesWorkedPM);
//             }
//         }

//         // Calculate total hours worked
//         $totalHoursWorked = $hoursWorkedAM + $hoursWorkedPM;

//         // Determine remark based on lateness
//         $remark = ($lateDurationAM > 0 || $lateDurationPM > 0) ? 'Late' : 'Present';

//         // Prepare the key for $attendanceData
//         $key = $attendance->employee_id . '-' . $checkInDate;

//         // Check if this entry already exists in $attendanceData
//         if (isset($attendanceData[$key])) {
//             // Update existing entry
//             $attendanceData[$key]->hours_workedAM += $hoursWorkedAM;
//             $attendanceData[$key]->hours_workedPM += $hoursWorkedPM;
//             $attendanceData[$key]->total_hours_worked += $totalHoursWorked;
//             $attendanceData[$key]->late_duration += $lateDurationAM;
//             $attendanceData[$key]->late_durationPM += $lateDurationPM;
//             $attendanceData[$key]->undertimeAM += $undertimeAM;
//             $attendanceData[$key]->undertimePM += $undertimePM;
//             $attendanceData[$key]->remarks = $remark;
//         } else {
//             // Create new entry
//             $attendanceData[$key] = (object) [
//                 'employee_id' => $attendance->employee_id,
//                 'worked_date' => $checkInDate,
//                 'hours_workedAM' => $hoursWorkedAM,
//                 'hours_workedPM' => $hoursWorkedPM,
//                 'total_hours_worked' => $totalHoursWorked,
//                 'late_duration' => $lateDurationAM,
//                 'late_durationPM' => $lateDurationPM,
//                 'undertimeAM' => $undertimeAM,
//                 'undertimePM' => $undertimePM,
//                 'remarks' => $remark,
//             ];
//         }

//         // Add total hours worked to overall total
//         $overallTotalHours += $totalHoursWorked;
   
// }

// // Optionally, you can store the $attendanceData and $overallTotalHours in the session or pass it to a view
// session()->put('attendance_data', $attendanceData);
// session()->put('overall_total_hours', $overallTotalHours);




//        $attendanceData = [];
// $overallTotalHours = 0;

// foreach ($attendanceTimeIn as $attendance) {
//     // Initialize variables for each record
//     $hoursWorkedAM = 0;
//     $hoursWorkedPM = 0;
//     $lateDurationAM = 0;
//     $lateDurationPM = 0;
//     $undertimeAM = 0;
//     $undertimePM = 0;

//     // Extract date and time from check-in
//     $checkInDateTime = new DateTime($attendance->check_in_time);
//     $checkInDate = $checkInDateTime->format('Y-m-d');
//     $dayOfWeek = $checkInDateTime->format('w');

//     // Find corresponding check-out time
//     $checkOut = $attendanceTimeOut->where('employee_id', $attendance->employee_id)
//                                     ->where('check_out_time', '>=', $attendance->check_in_time)
//                                     ->first();

//     // Fetch department working hours for the specific day of the week
//     $departmentWorkingHour = DepartmentWorkingHour::where('department_id', $attendance->employee->department_id)
//                                                     ->where('day_of_week', $dayOfWeek)
//                                                     ->first();

//     if ($departmentWorkingHour && $checkOut) {
//         $checkOutDateTime = new DateTime($checkOut->check_out_time);

//         // AM Shift
//         $morningStartTime = new DateTime($departmentWorkingHour->morning_start_time);
//         $morningEndTime = new DateTime($departmentWorkingHour->morning_end_time);
//         if ($checkInDateTime < $morningEndTime) {
//             $effectiveCheckInTime = max($checkInDateTime, $morningStartTime);
//             $effectiveCheckOutTime = min($checkOutDateTime, $morningEndTime);
//             if ($effectiveCheckInTime < $effectiveCheckOutTime) {
//                 $intervalAM = $effectiveCheckInTime->diff($effectiveCheckOutTime);
//                 $hoursWorkedAM = $intervalAM->h + ($intervalAM->i / 60);

//                 // Calculate late duration for AM
//                 $latestAllowedCheckInAM = clone $morningStartTime;
//                 $latestAllowedCheckInAM->add(new DateInterval('PT15M'));
//                 if ($checkInDateTime > $latestAllowedCheckInAM) {
//                     $lateIntervalAM = $checkInDateTime->diff($latestAllowedCheckInAM);
//                     $lateDurationAM = $lateIntervalAM->h * 60 + $lateIntervalAM->i;
//                 }

//                 // Calculate undertime for AM
//                 $scheduledAMMinutes = ($morningStartTime->diff($morningEndTime)->h * 60) + $morningStartTime->diff($morningEndTime)->i;
//                 $actualMinutesWorkedAM = ($effectiveCheckOutTime->diff($effectiveCheckInTime)->h * 60) + $effectiveCheckOutTime->diff($effectiveCheckInTime)->i;
//                 $undertimeAM = max(0, $scheduledAMMinutes - $actualMinutesWorkedAM);
//             }
//         }

//         // PM Shift
//         $afternoonStartTime = new DateTime($departmentWorkingHour->afternoon_start_time);
//         $afternoonEndTime = new DateTime($departmentWorkingHour->afternoon_end_time);
//         if ($checkInDateTime < $afternoonEndTime && $checkOutDateTime > $afternoonStartTime) {
//             $effectiveCheckInTime = max($checkInDateTime, $afternoonStartTime);
//             $effectiveCheckOutTime = min($checkOutDateTime, $afternoonEndTime);
//             if ($effectiveCheckInTime < $effectiveCheckOutTime) {
//                 $intervalPM = $effectiveCheckInTime->diff($effectiveCheckOutTime);
//                 $hoursWorkedPM = $intervalPM->h + ($intervalPM->i / 60);

//                 // Calculate late duration for PM
//                 $latestAllowedCheckInPM = clone $afternoonStartTime;
//                 $latestAllowedCheckInPM->add(new DateInterval('PT15M'));
//                 if ($checkInDateTime > $latestAllowedCheckInPM) {
//                     $lateIntervalPM = $checkInDateTime->diff($latestAllowedCheckInPM);
//                     $lateDurationPM = $lateIntervalPM->h * 60 + $lateIntervalPM->i;
//                 }

//                 // Calculate undertime for PM
//                 $scheduledPMMinutes = ($afternoonStartTime->diff($afternoonEndTime)->h * 60) + $afternoonStartTime->diff($afternoonEndTime)->i;
//                 $actualMinutesWorkedPM = ($effectiveCheckOutTime->diff($effectiveCheckInTime)->h * 60) + $effectiveCheckOutTime->diff($effectiveCheckInTime)->i;
//                 $undertimePM = max(0, $scheduledPMMinutes - $actualMinutesWorkedPM);
//             }
//         }

//         // Calculate total hours worked
//         $totalHoursWorked = $hoursWorkedAM + $hoursWorkedPM;

//         // Determine remark based on lateness
//         $remark = ($lateDurationAM > 0 || $lateDurationPM > 0) ? 'Late' : 'Present';

//         // Prepare the key for $attendanceData
//         $key = $attendance->employee_id . '-' . $checkInDate;

//         // Check if this entry already exists in $attendanceData
//         if (isset($attendanceData[$key])) {
//             // Update existing entry
//             $attendanceData[$key]->hours_workedAM += $hoursWorkedAM;
//             $attendanceData[$key]->hours_workedPM += $hoursWorkedPM;
//             $attendanceData[$key]->total_hours_worked += $totalHoursWorked;
//             $attendanceData[$key]->late_duration += $lateDurationAM;
//             $attendanceData[$key]->late_durationPM += $lateDurationPM;
//             $attendanceData[$key]->undertimeAM += $undertimeAM;
//             $attendanceData[$key]->undertimePM += $undertimePM;
//             $attendanceData[$key]->remarks = $remark;
//         } else {
//             // Create new entry
//             $attendanceData[$key] = (object) [
//                 'employee_id' => $attendance->employee_id,
//                 'worked_date' => $checkInDate,
//                 'hours_workedAM' => $hoursWorkedAM,
//                 'hours_workedPM' => $hoursWorkedPM,
//                 'total_hours_worked' => $totalHoursWorked,
//                 'late_duration' => $lateDurationAM,
//                 'late_durationPM' => $lateDurationPM,
//                 'undertimeAM' => $undertimeAM,
//                 'undertimePM' => $undertimePM,
//                 'remarks' => $remark,
//             ];
//         }

//         // Add total hours worked to overall total
//         $overallTotalHours += $totalHoursWorked;
//     } else {
//         // No check_out_time found, mark as absent
//         $attendanceData[$attendance->employee_id . '-' . $checkInDate] = (object) [
//             'employee_id' => $attendance->employee_id,
//             'worked_date' => $checkInDate,
//             'hours_workedAM' => 0,
//             'hours_workedPM' => 0,
//             'total_hours_worked' => 0,
//             'late_duration' => 0,
//             'late_durationPM' => 0,
//             'undertimeAM' => 0,
//             'undertimePM' => 0,
//             'remarks' => 'Absent',
//         ];
//     }
// }

// // Optionally, you can store the $attendanceData and $overallTotalHours in the session or pass it to a view
// session()->put('attendance_data', $attendanceData);
// session()->put('overall_total_hours', $overallTotalHours);





                    
        // $attendanceData = [];
        // $overallTotalHours = 0;

        // foreach ($attendanceTimeIn as $attendance) {
        //     // Initialize AM and PM hours worked
        //     $hoursWorkedAM = 0;
        //     $hoursWorkedPM = 0;
        //     $lateDurationAM = 0; // Initialize late duration
        //     $lateDurationPM = 0; // Initialize late duration for PM
        //     $totalHoursLate = 0;

        //     // Extract the day of the week from the check-in time
        //     $checkInDateTime = new DateTime($attendance->check_in_time);

        //     $dayOfWeek = $checkInDateTime->format('w'); // 0 (for Sunday) through 6 (for Saturday)

        //     // Find corresponding check_out_time
        //     $checkOut = $attendanceTimeOut->where('employee_id', $attendance->employee_id)
        //                                 ->where('check_out_time', '>=', $attendance->check_in_time)
        //                                 ->first();

        //     // Fetch department working hours for the specific day of the week
        //     $departmentWorkingHour = DepartmentWorkingHour::where('department_id', $attendance->employee->department_id)
        //                                                 ->where('day_of_week', $dayOfWeek)
        //                                                 ->first();

        //     if ($departmentWorkingHour && $checkOut) {
                
        //         // Create DateTime objects for check-out time
        //         $checkOutDateTime = new DateTime($checkOut->check_out_time);

        //         // Fetch working hours from the department's schedule
        //         $morningStartTime = new DateTime($departmentWorkingHour->morning_start_time);
        //         $morningEndTime = new DateTime($departmentWorkingHour->morning_end_time);
        //         $afternoonStartTime = new DateTime($departmentWorkingHour->afternoon_start_time);
        //         $afternoonEndTime = new DateTime($departmentWorkingHour->afternoon_end_time);

        //         // Calculate the effective AM working hours
        //         if ($checkInDateTime < $morningEndTime) {
        //             $effectiveCheckInTime = max($checkInDateTime, $morningStartTime);
        //             $effectiveCheckOutTime = min($checkOutDateTime, $morningEndTime);
        //             $intervalAM = $effectiveCheckInTime->diff($effectiveCheckOutTime);
        //             $hoursWorkedAM = $intervalAM->h + ($intervalAM->i / 60);
        //         }
        
        //         // Calculate the latest allowed check-in time (scheduled time + 15 minutes)
        //         $latestAllowedCheckIn = clone $morningStartTime;
        //         $latestAllowedCheckIn->add(new DateInterval('PT15M'));

        //         // dd($latestAllowedCheckIn);

        //         // Calculate late duration
        //         if ($checkInDateTime > $latestAllowedCheckIn) {
        //             $lateInterval = $checkInDateTime->diff($latestAllowedCheckIn);
        //             $lateDurationAM = $lateInterval->h * 60 + $lateInterval->i; // Convert to minutes
        //         }

        //         // Calculate the PM working hours
        //         if ($checkInDateTime < $afternoonEndTime && $checkOutDateTime > $afternoonStartTime) {
        //             $effectiveCheckInTime = max($checkInDateTime, $afternoonStartTime);
        //             $effectiveCheckOutTime = min($checkOutDateTime, $afternoonEndTime);
        //             $intervalPM = $effectiveCheckInTime->diff($effectiveCheckOutTime);
        //             $hoursWorkedPM = $intervalPM->h + ($intervalPM->i / 60);
        //         }

        //         $latestAllowedCheckInPM = clone $afternoonStartTime;
        //         $latestAllowedCheckInPM->add(new DateInterval('PT15M'));

        //         // Calculate late duration for PM
        //         if ($checkInDateTime > $latestAllowedCheckInPM) {
        //             $lateIntervalPM = $checkInDateTime->diff($latestAllowedCheckInPM);
        //             $lateDurationPM = $lateIntervalPM->h * 60 + $lateIntervalPM->i; // Convert to minutes
                    
        //         }
                
                

        //         // Calculate total hours worked
        //         $totalHoursWorked = $hoursWorkedAM + $hoursWorkedPM;
        //         $totalHoursLate = $lateDurationAM + $lateDurationPM;
        //         session()->put('late_duration_pm', $lateDurationPM);
        //         session()->put('total_late', $totalHoursLate);
        //         // dd($totalHoursLate);
        //         // Determine the remark based on lateness
        //         // $remark = $lateDurationAM > 0 ? 'Late' : 'Present';


        //         $remark = ($lateDurationAM > 0 || $lateDurationPM > 0) ? 'Late' : 
        //                 (($lateDurationAM === 0 && $lateDurationPM === 0) ? 'Present' : '');


        //         // Prepare the key for $attendanceData
        //         $key = $attendance->employee_id . '-' . $checkInDateTime->format('Y-m-d');

        //         // Check if this entry already exists in $attendanceData
        //         if (isset($attendanceData[$key])) {
        //             // Update existing entry
        //             $attendanceData[$key]->hours_workedAM += $hoursWorkedAM;
        //             $attendanceData[$key]->hours_workedPM += $hoursWorkedPM;
        //             $attendanceData[$key]->total_hours_worked += $totalHoursWorked;
        //             $attendanceData[$key]->late_duration += $lateDurationAM; // Update late duration
        //             $attendanceData[$key]->late_durationPM += $lateDurationPM; // Update late duration
        //             $attendanceData[$key]->remarks = $remark; // Update remark

                    
        //         } else {
        //             // Create new entry
        //             $attendanceData[$key] = (object) [
        //                 'employee_id' => $attendance->employee_id,
        //                 'worked_date' => $checkInDateTime->format('Y-m-d'),
        //                 'hours_workedAM' => $hoursWorkedAM,
        //                 'hours_workedPM' => $hoursWorkedPM,
        //                 'total_hours_worked' => $totalHoursWorked,
        //                 'late_duration' => $lateDurationAM, // Store late duration
        //                 'late_durationPM' => $lateDurationPM, // Update late duration
        //                 'remarks' => $remark, // Set remark based on lateness
        //             ];

        //             session()->put('late_duration', $lateDurationAM);
                    
        //         }

        //         // Add total hours worked to overall total
        //         $overallTotalHours += $totalHoursWorked;


        //     } else {
        //         // No check_out_time found, mark as absent
        //         $checkInDate = $checkInDateTime->format('Y-m-d');
        //         $attendanceData[$checkInDate] = (object) [
        //             'employee_id' => $attendance->employee_id,
        //             'worked_date' => $checkInDate,
        //             'hours_workedAM' => 0,
        //             'hours_workedPM' => 0,
        //             'total_hours_worked' => 0,
        //             'late_duration' => 0, // No late duration if absent
        //             'remarks' => 'Absent',
        //         ];
        //     }
        // }






// $attendanceData = [];
// $overallTotalHours = 0;

// foreach ($attendanceTimeIn as $attendance) {
//     // Initialize AM and PM hours worked
//     $hoursWorkedAM = 0;
//     $hoursWorkedPM = 0;

//     // Extract the day of the week from the check-in time
//     $checkInDateTime = new DateTime($attendance->check_in_time);
//     $dayOfWeek = $checkInDateTime->format('w'); // 0 (for Sunday) through 6 (for Saturday)

//     // Find corresponding check_out_time
//     $checkOut = $attendanceTimeOut->where('employee_id', $attendance->employee_id)
//                                     ->where('check_out_time', '>=', $attendance->check_in_time)
//                                     ->first();

//     // Fetch department working hours for the specific day of the week
//     $departmentWorkingHour = DepartmentWorkingHour::where('department_id', $attendance->employee->department_id)
//                                                   ->where('day_of_week', $dayOfWeek)
//                                                   ->first(); 

//     if ($departmentWorkingHour && $checkOut) {
//         // Create DateTime objects
//         $checkOutDateTime = new DateTime($checkOut->check_out_time);

//         // Fetch working hours from the department's schedule
//         $morningStartTime = new DateTime($departmentWorkingHour->morning_start_time);
//         $morningEndTime = new DateTime($departmentWorkingHour->morning_end_time);
//         $afternoonStartTime = new DateTime($departmentWorkingHour->afternoon_start_time);
//         $afternoonEndTime = new DateTime($departmentWorkingHour->afternoon_end_time);

//         // Calculate hours worked in AM
//         if ($checkInDateTime < $morningStartTime) {
//             $checkInDateTime = $morningStartTime; // Adjust check-in to start of morning shift
//         }

//         if ($checkOutDateTime > $morningEndTime) {
//             $checkOutDateTime = $morningEndTime; // Adjust check-out to end of morning shift
//         }

//         if ($checkInDateTime < $morningEndTime) {
//             $intervalAM = $checkInDateTime->diff($morningEndTime);
//             $hoursWorkedAM = $intervalAM->h + ($intervalAM->i / 60);

//             if ($checkOutDateTime > $morningEndTime) {
//                 $checkInDateTime = $morningEndTime; // Start from morning end if check-out is after morning shift

//                 $intervalAM = $checkInDateTime->diff($checkOutDateTime);
//                 $hoursWorkedAM += $intervalAM->h + ($intervalAM->i / 60);
//             }
//         }

//         // Reset check-in time to the actual check-in time for the afternoon shift calculation
//         $checkInDateTime = new DateTime($attendance->check_in_time);

//         if ($checkInDateTime < $afternoonStartTime) {
//             $checkInDateTime = $afternoonStartTime; // Adjust check-in to start of afternoon shift
//         }

//         if ($checkOutDateTime > $afternoonEndTime) {
//             $checkOutDateTime = $afternoonEndTime; // Adjust check-out to end of afternoon shift
//         }

//         if ($checkInDateTime < $afternoonEndTime) {
//             $intervalPM = $checkInDateTime->diff($checkOutDateTime);
//             $hoursWorkedPM = $intervalPM->h + ($intervalPM->i / 60);
//         }

//         // Calculate total hours worked
//         $totalHoursWorked = $hoursWorkedAM + $hoursWorkedPM;

//         // Prepare the key for $attendanceData
//         $key = $attendance->employee_id . '-' . $checkInDateTime->format('Y-m-d');

//         // Check if this entry already exists in $attendanceData
//         if (isset($attendanceData[$key])) {
//             // Update existing entry
//             $attendanceData[$key]->hours_workedAM += $hoursWorkedAM;
//             $attendanceData[$key]->hours_workedPM += $hoursWorkedPM;
//             $attendanceData[$key]->total_hours_worked += $totalHoursWorked;
//         } else {
//             // Create new entry
//             $attendanceData[$key] = (object) [
//                 'employee_id' => $attendance->employee_id,
//                 'worked_date' => $checkInDateTime->format('Y-m-d'),
//                 'hours_workedAM' => $hoursWorkedAM,
//                 'hours_workedPM' => $hoursWorkedPM,
//                 'total_hours_worked' => $totalHoursWorked,
//                 'remarks' => 'Present', // Assuming it's always present when hours are recorded
//                 // 'workingHoursDetails' => $workingHoursDetails,
//             ];
//         }

//         // Add total hours worked to overall total
//         $overallTotalHours += $totalHoursWorked;
//     } else {
//         // No check_out_time found, mark as absent
//         $checkInDate = $checkInDateTime->format('Y-m-d');
//         $attendanceData[$checkInDate] = (object) [
//             'employee_id' => $attendance->employee_id,
//             'worked_date' => $checkInDate,
//             'hours_workedAM' => 0,
//             'hours_workedPM' => 0,
//             'total_hours_worked' => 0,
//             'remarks' => 'Absent',
//         ];
//     }
// }


// Now $overallTotalHours contains the sum of all total hours worked



        $schools = School::all();
        $departments = Department::where('school_id', $this->selectedSchool)
            ->whereIn('dept_identifier', ['employee', 'faculty'])
            ->get();


        $departmentDisplayWorkingHour = DepartmentWorkingHour::where('department_id', $this->selectedDepartment4)
                                                           ->get();

        return view('livewire.admin.show-employee-attendance', [
            'overallTotalHours' => $overallTotalHours,
            'attendanceData' =>$attendanceData,
            'attendanceTimeIn' => $attendanceTimeIn,
            'attendanceTimeOut' => $attendanceTimeOut,
            'schools' => $schools,
            'departments' => $departments,
            'schoolToShow' => $this->schoolToShow,
            'departmentToShow' => $this->departmentToShow,
            'selectedEmployeeToShow' => $this->selectedEmployeeToShow,
            'employees' => $employees, // Ensure employees variable is defined if needed
            'selectedAttendanceByDate' => $this->selectedAttendanceByDate,
            'departmentDisplayWorkingHour' => $departmentDisplayWorkingHour,
        ]);
    }



    public function generatePDF()
    {
        $savePath = storage_path('app/'); // Default save path (storage/app/)

        try {

           // Determine the filename dynamically with date included if both startDate and endDate are selected
            if ($this->startDate && $this->endDate) {
                $selectedStartDate = date('jS F Y', strtotime($this->startDate));
                $selectedEndDate = date('jS F Y', strtotime($this->endDate));
                $dateRange = $selectedStartDate . ' to ' . $selectedEndDate;
            } else {
                $dateRange = 'No Date Selected'; // Default text if no date range is selected
            }

            // Construct the filename with the date range if available
            $filename = $this->selectedEmployeeToShow->employee_lastname . ', ' . $this->selectedEmployeeToShow->employee_firstname . ' ' . $this->selectedEmployeeToShow->employee_middlename . ' - ' . $dateRange . '.pdf';


            // Base query for EmployeeAttendanceTimeIn with left join to EmployeeAttendanceTimeOut
            $queryTimeIn = EmployeeAttendanceTimeIn::query()
                ->with(['employee.school', 'employee.department']);
            $queryTimeOut = EmployeeAttendanceTimeOut::query()
                ->with(['employee.school', 'employee.department']);
                
            // Apply selected school filter
            if ($this->selectedSchool) {
                $queryTimeIn->whereHas('employee', function (Builder $query) {
                    $query->where('school_id', $this->selectedSchool);
                });
                $queryTimeOut->whereHas('employee', function (Builder $query) {
                    $query->where('school_id', $this->selectedSchool);
                });
                $this->schoolToShow = School::find($this->selectedSchool);
            } else {
                $this->schoolToShow = null;
            }

            // Apply selected department filter
            if ($this->selectedDepartment4) {
                $queryTimeIn->whereHas('employee', function (Builder $query) {
                    $query->where('department_id', $this->selectedDepartment4);
                });
                $queryTimeOut->whereHas('employee', function (Builder $query) {
                    $query->where('department_id', $this->selectedDepartment4);
                });
                $this->departmentToShow = Department::find($this->selectedDepartment4);
                $employees = Employee::where('department_id', $this->selectedDepartment4)->get();
            } else {
                $this->departmentToShow = null;
                $employees = Employee::all();
            }

            // Apply selected employee filter
            if ($this->selectedEmployee) {
                $queryTimeIn->where('employee_id', $this->selectedEmployee);
                $this->selectedEmployeeToShow = Employee::find($this->selectedEmployee);
                $queryTimeOut->where('employee_id', $this->selectedEmployee);
                $this->selectedEmployeeToShow = Employee::find($this->selectedEmployee);
            } else {
                $this->selectedEmployeeToShow = null;
            }

            // Apply date range filter if both dates are set
            if ($this->startDate && $this->endDate) {
                $queryTimeIn->whereDate('check_in_time', '>=', $this->startDate)
                            ->whereDate('check_in_time', '<=', $this->endDate);

                $queryTimeOut->whereDate('check_out_time', '>=', $this->startDate)
                            ->whereDate('check_out_time', '<=', $this->endDate);
                            
                $selectedAttendanceByDate = $queryTimeIn->get();// Fetch data and assign to selectedAttendanceByDate
                
                $this->selectedAttendanceByDate = $selectedAttendanceByDate;   
            }
            


            $attendanceTimeIn = $queryTimeIn->orderBy($this->sortField, $this->sortDirection)
                ->paginate(50);
            $attendanceTimeOut = $queryTimeOut->orderBy($this->sortField, $this->sortDirection)
                ->paginate(50);


            $attendanceData = [];
            $overallTotalHours = 0;

            foreach ($attendanceTimeIn as $attendance) {
                // Initialize AM and PM hours worked
                $hoursWorkedAM = 0;
                $hoursWorkedPM = 0;

                // Find corresponding check_out_time
                $checkOut = $attendanceTimeOut->where('employee_id', $attendance->employee_id)
                                            ->where('check_out_time', '>=', $attendance->check_in_time)
                                            ->first();

                // Calculate hours worked
                if ($checkOut) {
                    // Extract dates from check_in_time and check_out_time
                    $checkInDate = date('Y-m-d', strtotime($attendance->check_in_time));
                    $checkOutDate = date('Y-m-d', strtotime($checkOut->check_out_time));

                    // Check if dates match
                    if ($checkInDate === $checkOutDate) {
                        // Calculate hours worked
                        $checkIn = strtotime($attendance->check_in_time);
                        $checkOutTime = strtotime($checkOut->check_out_time);

                        // Split hours into AM and PM
                        if ($checkIn < strtotime($checkInDate . ' 12:00 PM')) {
                            if ($checkOutTime <= strtotime($checkInDate . ' 1:00 PM')) {
                                // Both check-in and check-out are in the AM
                                $hoursWorkedAM = ($checkOutTime - $checkIn) / 3600;
                            } else {
                                // Check-in is in AM and check-out is in PM
                                $hoursWorkedAM = (strtotime($checkInDate . ' 12:00 PM') - $checkIn) / 3600;
                                $hoursWorkedPM = ($checkOutTime - strtotime($checkInDate . ' 01:00 PM')) / 3600;
                            }
                        } else {
                            // Both check-in and check-out are in the PM
                            $hoursWorkedPM = ($checkOutTime - $checkIn) / 3600;
                        }

                        // Calculate total hours worked
                        $totalHoursWorked = $hoursWorkedAM + $hoursWorkedPM;

                        // Prepare the key for $attendanceData
                        $key = $attendance->employee_id . '-' . $checkInDate;

                        // Check if this entry already exists in $attendanceData
                        if (isset($attendanceData[$key])) {
                            // Update existing entry
                            $attendanceData[$key]->hours_workedAM += $hoursWorkedAM;
                            $attendanceData[$key]->hours_workedPM += $hoursWorkedPM;
                            $attendanceData[$key]->total_hours_worked += $totalHoursWorked;
                        } else {
                            // Create new entry
                            $attendanceData[$key] = (object) [
                                'employee_id' => $attendance->employee_id,
                                'worked_date' => $checkInDate,
                                'hours_workedAM' => $hoursWorkedAM,
                                'hours_workedPM' => $hoursWorkedPM,
                                'total_hours_worked' => $totalHoursWorked,
                                'remarks' => 'Present', // Assuming it's always present when hours are recorded
                            ];
                        }

                        // Add total hours worked to overall total
                        $overallTotalHours += $totalHoursWorked;
                    } else {
                        // Dates do not match, mark as absent
                        $attendanceData[] = (object) [
                            'employee_id' => $attendance->employee_id,
                            'worked_date' => $checkInDate,
                            'hours_workedAM' => 0,
                            'hours_workedPM' => 0,
                            'total_hours_worked' => 0,
                            'remarks' => 'Absent',
                        ];
                    }
                } else {
                    // No check_out_time found, mark as absent
                    $checkInDate = date('Y-m-d', strtotime($attendance->check_in_time));
                    $attendanceData[] = (object) [
                        'employee_id' => $attendance->employee_id,
                        'worked_date' => $checkInDate,
                        'hours_workedAM' => 0,
                        'hours_workedPM' => 0,
                        'total_hours_worked' => 0,
                        'remarks' => 'Absent',
                    ];
                }
            }

                $pdf = \PDF::loadView('generate-pdf', [
                'overallTotalHours' => $overallTotalHours,
                'selectedStartDate' => $this->startDate,
                'selectedEndDate' => $this->endDate,
                'attendanceData' => $attendanceData,
                'attendanceTimeIn' => $attendanceTimeIn,
                'attendanceTimeOut' => $attendanceTimeOut,
                'selectedEmployeeToShow' => $this->selectedEmployeeToShow,
            ])->setPaper('letter', 'landscape'); // Set paper size and orientation

             $pdf->save($savePath . '/' . $filename);

            // Download the PDF file with the given filename
            return response()->download($savePath . '/' . $filename, $filename);
        } catch (\Exception $e) {
            // Log or handle the exception as needed
            dd($e->getMessage()); // Output the error for debugging
        }
    }
    
    




    public function updateEmployees()
    {
        if ($this->selectedSchool) {
            $this->departmentsToShow = Department::where('school_id', $this->selectedSchool)->get();
        } else {
            $this->departmentsToShow = collect();
        }

        $this->selectedDepartment4 = null;
        $this->departmentToShow = null;
        $this->startDate = null; // Reset start date
        $this->endDate = null; // Reset end date
    }

    public function updateEmployeesByDepartment()
    {
        if ($this->selectedDepartment4 && $this->selectedSchool) {
            $this->departmentToShow = Department::where('id', $this->selectedDepartment4)
                ->where('school_id', $this->selectedSchool)
                ->first();
        } else {
            $this->departmentToShow = null;
            $this->startDate = null; // Reset start date
            $this->endDate = null; // Reset end date
        }
    }

    public function updateAttendanceByEmployee()
    {
        if ($this->selectedEmployee) {
            $this->attendancesToShow = EmployeeAttendanceTimeIn::where('employee_id', $this->selectedEmployee)->get();
            $this->attendancesToShow = EmployeeAttendanceTimeOut::where('employee_id', $this->selectedEmployee)->get();
        } else {
            $this->attendancesToShow = collect();
            $this->startDate = null; // Reset start date
            $this->endDate = null; // Reset end date
        }
    }

public function updateAttendanceByDateRange()
{
    if ($this->startDate && $this->endDate) {
        // Base query for EmployeeAttendanceTimeIn with left join to EmployeeAttendanceTimeOut
        $queryTimeIn = EmployeeAttendanceTimeIn::query()
            ->with(['employee.school', 'employee.department'])
            ->where('employee_id', $this->selectedEmployee)
            ->whereDate('check_in_time', '>=', $this->startDate)
            ->whereDate('check_in_time', '<=', $this->endDate);

        $this->selectedAttendanceByDate = $queryTimeIn->get();
    } else {
        $this->selectedAttendanceByDate = collect(); // Empty collection if no date range selected
    }
}


    // protected function applySearchFilters($queryTimeIn)
    // {
    //     return $queryTimeIn->whereHas('employee', function (Builder $query) {
    //         $query->where('employee_id', 'like', '%' . $this->search . '%')
    //             ->orWhereHas('school', function (Builder $query) {
    //                 $query->where('school_name', 'like', '%' . $this->search . '%');
    //             })
    //             ->orWhereHas('department', function (Builder $query) {
    //                 $query->where('department_name', 'like', '%' . $this->search . '%');
    //             });
    //     });
    // }
}