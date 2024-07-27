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
use Illuminate\Support\Facades\DB;

class SearchEmployeeAttendance extends Component
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
    public $departmentDisplayWorkingHour = [];
    public $selectedEmployeeId = '';


    protected $listeners = ['searchById'];

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

    public function searchById($employeeId)
    {   
    
        $this->search = $employeeId;
        $this->selectedEmployeeId = $employeeId; // Optional: to highlight selected row or for other purposes
    }

    public function render()
    {




        // Base query for EmployeeAttendanceTimeIn with left join to EmployeeAttendanceTimeOut
        $queryTimeIn = EmployeeAttendanceTimeIn::query()
            ->with(['employee.school', 'employee.department']);

        $queryTimeOut = EmployeeAttendanceTimeOut::query()
            ->with(['employee.school', 'employee.department']);
            

        // Apply search filters
        $queryTimeIn = $this->applySearchFiltersIn($queryTimeIn);
        $queryTimeOut = $this->applySearchFiltersOut($queryTimeOut);


        

       // Apply selected employee filter
    if ($this->search) {


        // Search employees based on search term in multiple fields
        $this->employees = Employee::where(function ($query) {
            $query->where('employee_id', 'like', '%' . $this->search . '%')
                ->orWhere(DB::raw('CONCAT(employee_lastname, ", ", employee_firstname, " ", employee_middlename)'), 'like', '%' . $this->search . '%');
        })->get();
        
        if ($this->employees->isNotEmpty()) {
            // Assuming $queryTimeIn and $queryTimeOut are previously defined queries
            $queryTimeIn->whereIn('employee_id', $this->employees->pluck('id'));
            $queryTimeOut->whereIn('employee_id', $this->employees->pluck('id'));

            // Optionally, select the first employee to show details
            $this->selectedEmployeeToShow = $this->employees->first();
            
            // Fetch and display the department hours for the selected employee's department
            $departmentId = $this->selectedEmployeeToShow->department_id;
            $departmentDisplayWorkingHour = DepartmentWorkingHour::where('department_id', $departmentId)->get();
        } else {
            $this->selectedEmployeeToShow = null;
            $this->departmentDisplayWorkingHour = [];
        }
    } else {
        $this->employees = [];
        $this->selectedEmployeeToShow = null;
        $departmentDisplayWorkingHour = [];
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
            ->paginate(5000);


        $attendanceTimeOut = $queryTimeOut->orderBy($this->sortField, $this->sortDirection)
            ->paginate(5000);



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

                

                    // AM Shift Calculation  for 15 mins interval of declaring late
                    if ($checkInDateTime < $morningEndTime) {
                        $effectiveCheckInTime = max($checkInDateTime, $morningStartTime);
                        $effectiveCheckOutTime = min($checkOutDateTime, $morningEndTime);
                        if ($effectiveCheckInTime < $effectiveCheckOutTime) {
                            $intervalAM = $effectiveCheckInTime->diff($effectiveCheckOutTime);
                            // $hoursWorkedAM = $intervalAM->h + ($intervalAM->i / 60);
                            $hoursWorkedAM = $intervalAM->h + ($intervalAM->i / 60) + ($intervalAM->s / 3600);
                            // Calculate late duration for AM
                            // $latestAllowedCheckInAM = clone $morningStartTime;
                            // $latestAllowedCheckInAM->add(new DateInterval('PT15M'));

                            if ($checkInDateTime > $morningStartTime) {

                                $lateIntervalAM = $checkInDateTime->diff($morningStartTime);
                                $lateDurationAM = ($lateIntervalAM->h * 60) + $lateIntervalAM->i + ($lateIntervalAM->s / 60);
                                // $lateDurationAM = $lateIntervalAM->h * 60 + $lateIntervalAM->i;

                                
                            }

                            // // Calculate undertime for AM
                            // $scheduledAMMinutes = ($morningStartTime->diff($morningEndTime)->h * 60) + $morningStartTime->diff($morningEndTime)->i;
                            // $actualMinutesWorkedAM = ($effectiveCheckOutTime->diff($effectiveCheckInTime)->h * 60) + $effectiveCheckOutTime->diff($effectiveCheckInTime)->i;
                            // $undertimeAM = max(0, $scheduledAMMinutes - $actualMinutesWorkedAM);

                        }
                        // Calculate scheduled AM minutes
                            // $scheduledAMMinutes = ($morningStartTime->diff($morningEndTime)->h * 60) + $morningStartTime->diff($morningEndTime)->i;

                            // // Calculate actual minutes worked up to the morning end time
                            // if ($effectiveCheckOutTime < $morningEndTime) {
                            //     $actualMinutesUpToEnd = ($effectiveCheckOutTime->diff($morningStartTime)->h * 60) + $effectiveCheckOutTime->diff($morningStartTime)->i;
                            // } else {
                            //     $actualMinutesUpToEnd = ($morningEndTime->diff($morningStartTime)->h * 60) + $morningEndTime->diff($morningStartTime)->i;
                            // }

                            // // Calculate undertime for AM
                            // $undertimeAM = max(0, $scheduledAMMinutes - $actualMinutesUpToEnd);

                            $scheduledDiff = $morningStartTime->diff($morningEndTime);
                            $scheduledAMMinutes = ($scheduledDiff->h * 60) + $scheduledDiff->i + ($scheduledDiff->s / 60);

                                // Calculate actual worked time up to the morning end time including seconds
                                if ($effectiveCheckOutTime < $morningEndTime) {
                                    $actualDiff = $effectiveCheckOutTime->diff($morningStartTime);
                                } else {
                                    $actualDiff = $morningEndTime->diff($morningStartTime);
                                }
                                $actualMinutesUpToEnd = ($actualDiff->h * 60) + $actualDiff->i + ($actualDiff->s / 60);

                                // Calculate undertime in minutes
                                $undertimeAM = max(0, $scheduledAMMinutes - $actualMinutesUpToEnd);
                
                    }  


                    //no interval, automatic late after the morning start time
                    // if ($checkInDateTime < $morningEndTime) {
                    //     $effectiveCheckInTime = max($checkInDateTime, $morningStartTime);
                    //     $effectiveCheckOutTime = min($checkOutDateTime, $morningEndTime);
                        
                    //     if ($effectiveCheckInTime < $effectiveCheckOutTime) {
                    //         $intervalAM = $effectiveCheckInTime->diff($effectiveCheckOutTime);
                    //         $hoursWorkedAM = $intervalAM->h + ($intervalAM->i / 60);
                            
                    //         // Calculate late duration for AM
                    //         if ($checkInDateTime > $morningStartTime) {
                    //             $lateIntervalAM = $checkInDateTime->diff($morningStartTime);
                    //             $lateDurationAM = $lateIntervalAM->h * 60 + $lateIntervalAM->i;
                    //         } else {
                    //             $lateDurationAM = 0;
                    //         }

                    //         // Calculate scheduled AM minutes
                    //         $scheduledAMMinutes = ($morningStartTime->diff($morningEndTime)->h * 60) + $morningStartTime->diff($morningEndTime)->i;

                    //         // Calculate actual minutes worked up to the morning end time
                    //         if ($effectiveCheckOutTime < $morningEndTime) {
                    //             $actualMinutesUpToEnd = ($effectiveCheckOutTime->diff($morningStartTime)->h * 60) + $effectiveCheckOutTime->diff($morningStartTime)->i;
                    //         } else {
                    //             $actualMinutesUpToEnd = ($morningEndTime->diff($morningStartTime)->h * 60) + $morningEndTime->diff($morningStartTime)->i;
                    //         }

                    //         // Calculate undertime for AM
                    //         $undertimeAM = max(0, $scheduledAMMinutes - $actualMinutesUpToEnd);
                    //     }
                    // }

                    // PM Shift Calculation
                    if ($checkInDateTime < $afternoonEndTime && $checkOutDateTime > $afternoonStartTime) {
                        $effectiveCheckInTime = max($checkInDateTime, $afternoonStartTime);
                        $effectiveCheckOutTime = min($checkOutDateTime, $afternoonEndTime);
                        if ($effectiveCheckInTime < $effectiveCheckOutTime) {
                            $intervalPM = $effectiveCheckInTime->diff($effectiveCheckOutTime);
                            $hoursWorkedPM = $intervalPM->h + ($intervalPM->i / 60) + ($intervalPM->s / 3600);

                            // Calculate late duration for PM
                            // $latestAllowedCheckInPM = clone $afternoonStartTime;
                            // $latestAllowedCheckInPM->add(new DateInterval('PT15M'));
                            if ($checkInDateTime > $afternoonStartTime) {
                                $lateIntervalPM = $checkInDateTime->diff($afternoonStartTime);
                                $lateDurationPM = ($lateIntervalPM->h * 60) + $lateIntervalPM->i + ($lateIntervalPM->s / 60);
                            }

                            // // Calculate undertime for PM
                            // $scheduledPMMinutes = ($afternoonStartTime->diff($afternoonEndTime)->h * 60) + $afternoonStartTime->diff($afternoonEndTime)->i;
                            // $actualMinutesWorkedPM = ($effectiveCheckOutTime->diff($effectiveCheckInTime)->h * 60) + $effectiveCheckOutTime->diff($effectiveCheckInTime)->i;
                            // $undertimePM = max(0, $scheduledPMMinutes - $actualMinutesWorkedPM);
                        }

                        // $scheduledPMMinutes = ($afternoonStartTime->diff($afternoonEndTime)->h * 60) + $afternoonStartTime->diff($afternoonEndTime)->i;

                        // // Calculate actual minutes worked up to the morning end time
                        // if ($effectiveCheckOutTime < $afternoonEndTime) {
                        //     $actualMinutesUpToEnd = ($effectiveCheckOutTime->diff($afternoonStartTime)->h * 60) + $effectiveCheckOutTime->diff($afternoonStartTime)->i;
                        // } else {
                        //     $actualMinutesUpToEnd = ($afternoonEndTime->diff($afternoonStartTime)->h * 60) + $afternoonEndTime->diff($afternoonStartTime)->i;
                        // }

                        // // Calculate undertime for AM
                        // $undertimePM = max(0, $scheduledPMMinutes - $actualMinutesUpToEnd);

                        $scheduledPMDiff = $afternoonStartTime->diff($afternoonEndTime);
                        $scheduledPMMinutes = ($scheduledPMDiff->h * 60) + $scheduledPMDiff->i + ($scheduledPMDiff->s / 60);

                        // Calculate actual worked time up to the afternoon end time including seconds
                        if ($effectiveCheckOutTime < $afternoonEndTime) {
                            $actualPMDiff = $effectiveCheckOutTime->diff($afternoonStartTime);
                        } else {
                            $actualPMDiff = $afternoonEndTime->diff($afternoonStartTime);
                        }
                        $actualMinutesUpToEndPM = ($actualPMDiff->h * 60) + $actualPMDiff->i + ($actualPMDiff->s / 60);

                        // Calculate undertime in minutes
                        $undertimePM = max(0, $scheduledPMMinutes - $actualMinutesUpToEndPM);

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



        $schools = School::all();
        $departments = Department::where('school_id', $this->selectedSchool)
            ->whereIn('dept_identifier', ['employee', 'faculty'])
            ->get();




        return view('livewire.admin.search-employee-attendance', [
            'overallTotalHours' => $overallTotalHours,
            'attendanceData' =>$attendanceData,
            'attendanceTimeIn' => $attendanceTimeIn,
            'attendanceTimeOut' => $attendanceTimeOut,
            'schools' => $schools,
            'departments' => $departments,
            'schoolToShow' => $this->schoolToShow,
            'departmentToShow' => $this->departmentToShow,
            'selectedEmployeeToShow' => $this->selectedEmployeeToShow,
            // 'employees' => $employees, // Ensure employees variable is defined if needed
            'selectedAttendanceByDate' => $this->selectedAttendanceByDate,
            'departmentDisplayWorkingHour' => $this->departmentDisplayWorkingHour,
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


    protected function applySearchFiltersIn($queryTimeIn)
    {
        return $queryTimeIn->whereHas('employee', function (Builder $query) {
            $query->where('employee_id', 'like', '%' . $this->search . '%')
                    ->orWhere('employee_lastname', 'like', '%' . $this->search . '%')
                    ->orWhere('employee_firstname', 'like', '%' . $this->search . '%')
                    ->orWhere('employee_middlename', 'like', '%' . $this->search . '%')
                    ->orWhereHas('department', function (Builder $query) {
                    $query->where('department_abbreviation', 'like', '%' . $this->search . '%')
                        ->orWhere('department_name', 'like', '%' . $this->search . '%');
                });
        });
    }

    protected function applySearchFiltersOut($queryTimeOut)
    {
        return $queryTimeOut->whereHas('employee', function (Builder $query) {
            $query->where('employee_id', 'like', '%' . $this->search . '%')
                    ->orWhere('employee_lastname', 'like', '%' . $this->search . '%')
                    ->orWhere('employee_firstname', 'like', '%' . $this->search . '%')
                    ->orWhere('employee_middlename', 'like', '%' . $this->search . '%')
                    ->orWhereHas('department', function (Builder $query) {
                    $query->where('department_abbreviation', 'like', '%' . $this->search . '%')
                        ->orWhere('department_name', 'like', '%' . $this->search . '%');
                });
        });
    }
}
