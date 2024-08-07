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
use App\Exports\AttendanceExport;
use Maatwebsite\Excel\Facades\Excel;

class DisplayDataforPayroll extends Component
{
    use WithPagination;

    public $search = '';
        public $searchh = '';
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
        $this->searchh = '';
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
        $queryTimeIn = $this->applySearchFiltersIn($queryTimeIn);
        $queryTimeOut = $this->applySearchFiltersOut($queryTimeOut);

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
        // if ($this->selectedDepartment4) {
        //     $queryTimeIn->whereHas('employee', function (Builder $query) {
        //         $query->where('department_id', $this->selectedDepartment4);
        //     });
        //     $queryTimeOut->whereHas('employee', function (Builder $query) {
        //         $query->where('department_id', $this->selectedDepartment4);
        //     });
        //     $this->departmentToShow = Department::find($this->selectedDepartment4);
        //     $employees = Employee::where('department_id', $this->selectedDepartment4)->get();
        // } else {
        //     $this->departmentToShow = null;
        //     $employees = Employee::all();
        // }

        // Number of records per page
        $perPage = 500;

        if ($this->selectedDepartment4) {
            // Get employees in the selected department with pagination
            $employees = Employee::where('department_id', $this->selectedDepartment4)->paginate($perPage);

            // Get all employee IDs in the selected department
            $employeeIds = $employees->pluck('id')->toArray();

            // Filter time-in and time-out records by department and employee IDs
            $queryTimeIn->whereIn('employee_id', $employeeIds)
                        ->whereHas('employee', function (Builder $query) {
                            $query->where('department_id', $this->selectedDepartment4);
                        });

            $queryTimeOut->whereIn('employee_id', $employeeIds)
                        ->whereHas('employee', function (Builder $query) {
                            $query->where('department_id', $this->selectedDepartment4);
                        });

            // Paginate the time-in and time-out records
            // $this->attendanceTimeIn = $queryTimeIn->paginate($perPage);
            // $this->attendanceTimeOut = $queryTimeOut->paginate($perPage);

            // Get department details
            $this->departmentToShow = Department::find($this->selectedDepartment4);
                        
            // Reset selected employee
            $this->selectedEmployeeToShow = null;
        } else {
            // No department selected, show all employees with pagination
            $this->departmentToShow = null;
            $employees = Employee::paginate($perPage);

            // Reset pagination for time-in and time-out records
            $this->attendanceTimeIn = $queryTimeIn->paginate($perPage);
            $this->attendanceTimeOut = $queryTimeOut->paginate($perPage);

            $this->selectedEmployeeToShow = null;
        }

        // Pass the paginated employees to the view
        $this->employees = $employees;



        // Apply selected employee filter
        // if ($this->selectedEmployee) {
        //     $queryTimeIn->where('employee_id', $this->selectedEmployee);
        //     $this->selectedEmployeeToShow = Employee::find($this->selectedEmployee);
        //     $queryTimeOut->where('employee_id', $this->selectedEmployee);
        //     $this->selectedEmployeeToShow = Employee::find($this->selectedEmployee);
        // } else {
        //     $this->selectedEmployeeToShow = null;
        // }


        // Apply date range filter if both dates are set
        if ($this->startDate && $this->endDate) {
            $queryTimeIn->whereDate('check_in_time', '>=', $this->startDate)
                        ->whereDate('check_in_time', '<=', $this->endDate);

            $queryTimeOut->whereDate('check_out_time', '>=', $this->startDate)
                        ->whereDate('check_out_time', '<=', $this->endDate);
                        
            $selectedAttendanceByDate = $queryTimeIn->get();// Fetch data and assign to selectedAttendanceByDate
            
            $this->selectedAttendanceByDate = $selectedAttendanceByDate;   
        }
        

        // $attendanceTimeIn = $queryTimeIn->orderBy('employee_id', 'asc')
        //     ->paginate(500);
        

        // $attendanceTimeOut = $queryTimeOut->orderBy('employee_id', 'asc')
        //     ->paginate(10);

        $attendanceTimeIn = $queryTimeIn->orderBy('employee_id', 'asc')->get();
        $attendanceTimeOut = $queryTimeOut->orderBy('employee_id', 'asc')->get();


            



        $attendanceData = [];
        $overallTotalHours = 0;
        $overallTotalLateHours = 0;
        $overallTotalUndertime = 0;
        $totalHoursTobeRendered = 0;
        $overallTotalHoursSum = 0;

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
            $totalLateandUndertime = 0;
            $latePM = 0;
            $lateAM = 0;
            $undertimeAMTotal = 0;
            $undertimePMTotal = 0;
            $totalundertime = 0;
            $totalhoursNeed = 0;
            $totalHoursNeedperDay = 0;
            $overallTotalHoursLate = 0;

            $totalHoursByDay = [];
            $overallTotalHoursSumm = 0;
            
            $departmentId = $attendance->employee->department_id;

            $workingHoursByDay = DepartmentWorkingHour::select(
                    'day_of_week',
                    'morning_start_time',
                    'morning_end_time',
                    'afternoon_start_time',
                    'afternoon_end_time'
                )
                ->where('department_id', $departmentId)
                ->where('day_of_week', '!=', 0)
                ->get()
                ->groupBy('day_of_week');

            

            foreach ($workingHoursByDay as $dayOfWeek => $workingHours) {
                $totalHours = 0;

                foreach ($workingHours as $workingHour) {
                    if ($workingHour->morning_start_time && $workingHour->morning_end_time) {
                        $morningStart = Carbon::parse($workingHour->morning_start_time);
                        $morningEnd = Carbon::parse($workingHour->morning_end_time);
                        $totalHours += $morningStart->diffInHours($morningEnd);
                        
                    }

                    if ($workingHour->afternoon_start_time && $workingHour->afternoon_end_time) {
                        $afternoonStart = Carbon::parse($workingHour->afternoon_start_time);
                        $afternoonEnd = Carbon::parse($workingHour->afternoon_end_time);
                        $totalHours += $afternoonStart->diffInHours($afternoonEnd);
                    }
                }

                $totalHoursByDay[$dayOfWeek] = $totalHours;
                $overallTotalHoursSumm += $totalHours;
            }

            // foreach ($totalHoursByDay as $dayOfWeek => $totalHours) {
            //     echo "Day of Week: $dayOfWeek\n";
            //     echo "Total Working Hours: $totalHours hours\n";
            //     echo "------------------------\n";
            // }
            // echo "Overall Total Working Hours: $overallTotalHours hours\n";

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

                    $morStart = $morningStartTime->setTime(
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
                    
                    $morningStartTimew = $departmentWorkingHour->morning_start_time;
                    $morningEndTimew = $departmentWorkingHour->morning_end_time;
                    $afternoonStartTimew = $departmentWorkingHour->afternoon_start_time;
                    $afternoonEndTimew = $departmentWorkingHour->afternoon_end_time;

                        // Convert times to Carbon instances
                    $morningStartw = new DateTime($morningStartTimew);
                    $morningEndw = new DateTime($morningEndTimew);
                    $afternoonStartw = new DateTime($afternoonStartTimew);
                    $afternoonEndw = new DateTime($afternoonEndTimew);

                    // Calculate the duration in minutes for morning and afternoon
                    $morningInterval = $morningStartw->diff($morningEndw);
                    $morningDurationInMinutes = ($morningInterval->h * 60) + $morningInterval->i;
                    $afternoonInterval = $afternoonStartw->diff($afternoonEndw);
                    $afternoonDurationInMinutes = ($afternoonInterval->h * 60) + $afternoonInterval->i;

                    // Convert minutes to hours
                    $morningDuration = $morningDurationInMinutes / 60;
                    $afternoonDuration = $afternoonDurationInMinutes / 60;
                    // Calculate total hours needed
                    $totalHoursNeed = $morningDuration + $afternoonDuration;
                    $totalHoursTobeRendered = $totalHoursNeed;
                    $totalHoursNeedperDay = $totalHoursNeed;


                    if ($this->startDate && $this->endDate) {
                        $employeeId = $attendance->employee_id; // Assuming you have this from $attendance

                        // Determine if the start date and end date are the same
                        $isSameDate = $this->startDate === $this->endDate; // Adjust if necessary for your date format
                        $startDate = Carbon::parse($this->startDate)->startOfDay(); // Start of the selected start date
                        $endDate = Carbon::parse($this->endDate)->endOfDay(); // End of the selected end date

                        if ($isSameDate) {
                            // If the start date and end date are the same, only consider that specific day
                            $checkInCount = EmployeeAttendanceTimeIn::select(DB::raw('COUNT(DISTINCT DATE(check_in_time)) as unique_check_in_days'))
                                ->where('employee_id', $employeeId)
                                ->whereDate('check_in_time', $this->startDate)
                                ->first();
                        } else {
                            // If the start date and end date are different, consider the range
                            // $checkInCount = EmployeeAttendanceTimeIn::select(DB::raw('COUNT(DISTINCT DATE(check_in_time)) as unique_check_in_days'))
                            //     ->where('employee_id', $employeeId)
                            //     ->whereBetween('check_in_time', [$startDate, $endDate])
                            //     ->first();
                            $checkInCount = EmployeeAttendanceTimeIn::select(DB::raw('COUNT(DISTINCT DATE(employees_time_in_attendance.check_in_time)) as unique_check_in_days'))
                                        ->join('employees', 'employees_time_in_attendance.employee_id', '=', 'employees.id')
                                        ->join('working_hour', function($join) {
                                            $join->on('employees.department_id', '=', 'working_hour.department_id');
                                        })
                                        ->where('employees_time_in_attendance.employee_id', $employeeId)
                                        ->whereNotIn('employees_time_in_attendance.status', ['Absent', 'AWOL', 'On Leave'])
                                        ->whereNotIn('working_hour.day_of_week', [0, 6])
                                        ->whereBetween('check_in_time', [$startDate, $endDate]) // Exclude Saturday (6) and Sunday (7)
                                        ->first();
                                    

                        }

                        // Get the unique check-in days count
                        $uniqueCheckInDays = (int) $checkInCount->unique_check_in_days;
                        
                        // Calculate total hours to be rendered
                        $totalHoursTobeRendered = $totalHoursNeed * $uniqueCheckInDays;
                    } else {
                        
                        $employeeId = $attendance->employee_id; // Assuming you have this from $attendance
                        

                        $noww = new DateTime('now', new DateTimeZone('Asia/Taipei'));
                        $currentDatee = $noww->format('Y-m-d') . ' 00:00:00';

                        $checkInCount = EmployeeAttendanceTimeIn::select(DB::raw('COUNT(DISTINCT DATE(check_in_time)) as unique_check_in_days'))
                            ->where('employee_id', $employeeId)
                            ->where('check_in_time', '<>', $currentDatee)
                            ->whereNotIn('status', ['Absent', 'AWOL', 'On Leave'])
                            ->first();

                        $uniqueCheckInDays = (int) $checkInCount->unique_check_in_days;
                        $totalHoursTobeRendered = $totalHoursNeed * $uniqueCheckInDays;
                              
   
                        
                            

                   
                    }
                         
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
                                $lateAM = $lateIntervalAM->h + ($lateIntervalAM->i / 60) + ($lateIntervalAM->s / 3600);

                                
                            }

 

                        }


                            $scheduledDiff = $morningStartTime->diff($morningEndTime);
                            $scheduledAMMinutes = ($scheduledDiff->h * 60) + $scheduledDiff->i + ($scheduledDiff->s / 60);

                                // Calculate actual worked time up to the morning end time including seconds
                                if ($effectiveCheckOutTime < $morningEndTime) {
                                    $actualDiff = $effectiveCheckOutTime->diff($morningStartTime);
                                } else {
                                    $actualDiff = $morningEndTime->diff($morningStartTime);
                                }
                                $actualMinutesUpToEnd = ($actualDiff->h * 60) + $actualDiff->i + ($actualDiff->s / 60);
                                 $undertimeAMTotal = max(0, $scheduledAMMinutes - $actualMinutesUpToEnd);
                                // Calculate undertime in minutes
                                $undertimeAM = max(0, $scheduledAMMinutes - $actualMinutesUpToEnd);
                
                    }  


                 

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
                                $latePM = $lateIntervalPM->h + ($lateIntervalPM->i / 60) + ($lateIntervalPM->s / 3600);
                            }

                            
                        }

                        

                        $scheduledPMDiff = $afternoonStartTime->diff($afternoonEndTime);
                        $scheduledPMMinutes = ($scheduledPMDiff->h * 60) + $scheduledPMDiff->i + ($scheduledPMDiff->s / 60);

                        // Calculate actual worked time up to the afternoon end time including seconds
                        if ($effectiveCheckOutTime < $afternoonEndTime) {
                            $actualPMDiff = $effectiveCheckOutTime->diff($afternoonStartTime);
                        } else {
                            $actualPMDiff = $afternoonEndTime->diff($afternoonStartTime);
                        }
                        $actualMinutesUpToEndPM = ($actualPMDiff->h * 60) + $actualPMDiff->i + ($actualPMDiff->s / 60);
                        $undertimePMTotal = max(0, $scheduledPMMinutes - $actualMinutesUpToEndPM);
                        // Calculate undertime in minutes
                        $undertimePM = max(0, $scheduledPMMinutes - $actualMinutesUpToEndPM);

                    }

                    // Calculate total hours worked
                    $totalHoursWorked = $hoursWorkedAM + $hoursWorkedPM;
                    
                    $totalHoursLate = $lateDurationAM + $lateDurationPM;
                    $totalUndertimeHours = $undertimeAM + $undertimePM;
                    $overallTotalHoursLate = $lateAM + $latePM;
                    $totalundertime = $undertimeAMTotal + $undertimePMTotal;

                    // $totalhoursNeed = $morningDuration + $afternoonDuration;
    
                    // Determine remark based on lateness
                    $remark = ($lateDurationAM > 0 || $lateDurationPM > 0) ? 'Late' : 'Present';

                    $modifyStatus = $attendance->status;

          

                    // Prepare the key for $attendanceData
                    $key = $attendance->employee_id . '-' . $checkInDate;

                    $employee_idd = $attendance->employee->employee_id;
                    $employee_id = $attendance->employee_id;
                    $employeeLastname = $attendance->employee->employee_lastname;
                    $employeeFirstname = $attendance->employee->employee_firstname;
                    $employeeMiddlename = $attendance->employee->employee_middlename;
                    $checkInTimer = $attendance->check_in_time;
                    // Check if this entry already exists in $attendanceData
                    if (isset($attendanceData[$key])) {
                        // Update existing entry
                        
                        $attendanceData[$key]->hours_perDay = $totalHoursNeedperDay;
                        $attendanceData[$key]->hours_workedAM += $hoursWorkedAM;
                        $attendanceData[$key]->hours_workedPM += $hoursWorkedPM;
                        $attendanceData[$key]->total_hours_worked += $totalHoursWorked;
                        $attendanceData[$key]->total_hours_late += $totalHoursLate;
                        $attendanceData[$key]->late_duration += $lateDurationAM;
                        $attendanceData[$key]->late_durationPM += $lateDurationPM;
                        $attendanceData[$key]->undertimeAM += $undertimeAM;
                        $attendanceData[$key]->undertimePM += $undertimePM;
                        $attendanceData[$key]->total_late += $totalHoursLate;
                        $attendanceData[$key]->remarks = $remark;
                        $attendanceData[$key]->modify_status = $modifyStatus;
                        $attendanceData[$key]->employee_idd = $employee_idd;
                        $attendanceData[$key]->employee_id = $employee_id;
                        $attendanceData[$key]->employee_lastname = $employeeLastname;
                        $attendanceData[$key]->employee_firstname = $employeeFirstname;
                        $attendanceData[$key]->employee_middlename = $employeeMiddlename;
                        $attendanceData[$key]->hours_late_overall += $overallTotalHoursLate;
                        $attendanceData[$key]->hours_undertime_overall += $totalundertime;
                        $attendanceData[$key]->check_in_time = $checkInTimer;


                        // dd($attendanceData[$key]->undertimeAM += $undertimeAM);
                    } else {
                        // Create new entry
                        $attendanceData[$key] = (object) [
                            'hours_perDay' => $totalHoursNeedperDay,
                            'employee_id' => $employee_id,
                            'employee_lastname' => $employeeLastname,
                            'employee_firstname' => $employeeFirstname, // Add employee_lastname
                            'employee_middlename' => $employeeMiddlename,
                            'worked_date' => $checkInDate,
                            'hours_workedAM' => $hoursWorkedAM,
                            'hours_workedPM' => $hoursWorkedPM,
                            'total_hours_worked' => $totalHoursWorked,
                            'total_hours_late' => $totalHoursLate,
                            'late_duration' => $lateDurationAM,
                            'late_durationPM' => $lateDurationPM,
                            'undertimeAM' => $undertimeAM,
                            'undertimePM' => $undertimePM,
                            'total_late' => $totalHoursLate,
                            'remarks' => $remark,
                            'modify_status'=> $modifyStatus,
                            'hours_late_overall' => $overallTotalHoursLate,
                            'hours_undertime_overall' => $totalundertime,
                            'check_in_time' => $checkInTimer,
                            'employee_idd' => $employee_idd,

                        ];

                        //  session()->put('late_duration', $lateDurationAM);
                    }

                    // Add total hours worked to overall total
                    $overallTotalHours += $totalHoursWorked;
                    $overallTotalLateHours += $overallTotalHoursLate;
                    $overallTotalUndertime += $totalundertime;
                    $overallTotalHoursSum = $overallTotalHoursSumm;
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


        $departmentDisplayWorkingHour = DepartmentWorkingHour::where('department_id', $this->selectedDepartment4)
                                                           ->get();

        return view('livewire.admin.display-datafor-payroll', [
            'overallTotalHours' => $overallTotalHours,
            'overallTotalLateHours' => $overallTotalLateHours,
            'overallTotalHoursSum' => $overallTotalHoursSum,
            'overallTotalUndertime' => $overallTotalUndertime,
            'totalHoursTobeRendered' => $totalHoursTobeRendered,
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
        $savePath = storage_path('/app/generatedPDF'); // Default save path (storage/app/)
        // $savePath = 'C:/Users/YourUsername/Downloads/'; // Windows example
        $departments = Department::where('id', $this->selectedDepartment4)->get();
        $department = Department::find($this->selectedDepartment4);
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
            

            // Check if the department was found
            if ($department) {
                // Access a specific column, e.g., 'name'
                $departmentName = $department->department_abbreviation;

                // Construct the filename with the department name and date range
                $filename = $departmentName . ' - ' . $dateRange . '.pdf';
            } else {
                // Handle the case where the department was not found
                $filename = 'Unknown Department - ' . $dateRange . '.pdf';
            }


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


            // Apply date range filter if both dates are set
            if ($this->startDate && $this->endDate) {
                $queryTimeIn->whereDate('check_in_time', '>=', $this->startDate)
                            ->whereDate('check_in_time', '<=', $this->endDate);

                $queryTimeOut->whereDate('check_out_time', '>=', $this->startDate)
                            ->whereDate('check_out_time', '<=', $this->endDate);
                            
                $selectedAttendanceByDate = $queryTimeIn->get();// Fetch data and assign to selectedAttendanceByDate
                
                $this->selectedAttendanceByDate = $selectedAttendanceByDate;   
            }
            
            // $attendanceTimeIn = $queryTimeIn->orderBy($this->sortField, $this->sortDirection)
            //     ->paginate(50);
            // $attendanceTimeOut = $queryTimeOut->orderBy($this->sortField, $this->sortDirection)
            //     ->paginate(50);


            // $attendanceTimeIn = $queryTimeIn->orderBy('employee_id', 'asc')
            //     ->paginate(10);

            // $attendanceTimeOut = $queryTimeOut->orderBy('employee_id', 'asc')
            //     ->paginate(10);

            $attendanceTimeIn = $queryTimeIn->orderBy('employee_id', 'asc')->get();
            $attendanceTimeOut = $queryTimeOut->orderBy('employee_id', 'asc')->get();   

            $attendanceData = [];
        $overallTotalHours = 0;
        $overallTotalLateHours = 0;
        $overallTotalUndertime = 0;
        $totalHoursTobeRendered = 0;
        $overallTotalHoursSum = 0;

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
            $totalLateandUndertime = 0;
            $latePM = 0;
            $lateAM = 0;
            $undertimeAMTotal = 0;
            $undertimePMTotal = 0;
            $totalundertime = 0;
            $totalhoursNeed = 0;
            $totalHoursNeedperDay = 0;

            $totalHoursByDay = [];
            $overallTotalHoursSumm = 0;
            
            $departmentId = $attendance->employee->department_id;

            $workingHoursByDay = DepartmentWorkingHour::select(
                    'day_of_week',
                    'morning_start_time',
                    'morning_end_time',
                    'afternoon_start_time',
                    'afternoon_end_time'
                )
                ->where('department_id', $departmentId)
                ->where('day_of_week', '!=', 0)
                ->get()
                ->groupBy('day_of_week');

            

            foreach ($workingHoursByDay as $dayOfWeek => $workingHours) {
                $totalHours = 0;

                foreach ($workingHours as $workingHour) {
                    if ($workingHour->morning_start_time && $workingHour->morning_end_time) {
                        $morningStart = Carbon::parse($workingHour->morning_start_time);
                        $morningEnd = Carbon::parse($workingHour->morning_end_time);
                        $totalHours += $morningStart->diffInHours($morningEnd);
                        
                    }

                    if ($workingHour->afternoon_start_time && $workingHour->afternoon_end_time) {
                        $afternoonStart = Carbon::parse($workingHour->afternoon_start_time);
                        $afternoonEnd = Carbon::parse($workingHour->afternoon_end_time);
                        $totalHours += $afternoonStart->diffInHours($afternoonEnd);
                    }
                }

                $totalHoursByDay[$dayOfWeek] = $totalHours;
                $overallTotalHoursSumm += $totalHours;
            }

            // foreach ($totalHoursByDay as $dayOfWeek => $totalHours) {
            //     echo "Day of Week: $dayOfWeek\n";
            //     echo "Total Working Hours: $totalHours hours\n";
            //     echo "------------------------\n";
            // }
            // echo "Overall Total Working Hours: $overallTotalHours hours\n";

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

                    $morStart = $morningStartTime->setTime(
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
                    
                    $morningStartTimew = $departmentWorkingHour->morning_start_time;
                    $morningEndTimew = $departmentWorkingHour->morning_end_time;
                    $afternoonStartTimew = $departmentWorkingHour->afternoon_start_time;
                    $afternoonEndTimew = $departmentWorkingHour->afternoon_end_time;

                        // Convert times to Carbon instances
                    $morningStartw = new DateTime($morningStartTimew);
                    $morningEndw = new DateTime($morningEndTimew);
                    $afternoonStartw = new DateTime($afternoonStartTimew);
                    $afternoonEndw = new DateTime($afternoonEndTimew);

                    // Calculate the duration in minutes for morning and afternoon
                    $morningInterval = $morningStartw->diff($morningEndw);
                    $morningDurationInMinutes = ($morningInterval->h * 60) + $morningInterval->i;
                    $afternoonInterval = $afternoonStartw->diff($afternoonEndw);
                    $afternoonDurationInMinutes = ($afternoonInterval->h * 60) + $afternoonInterval->i;

                    // Convert minutes to hours
                    $morningDuration = $morningDurationInMinutes / 60;
                    $afternoonDuration = $afternoonDurationInMinutes / 60;
                    // Calculate total hours needed
                    $totalHoursNeed = $morningDuration + $afternoonDuration;
                    $totalHoursTobeRendered = $totalHoursNeed;
                    $totalHoursNeedperDay = $totalHoursNeed;
                    if ($this->startDate && $this->endDate) {
                        $employeeId = $attendance->employee_id; // Assuming you have this from $attendance

                        // Determine if the start date and end date are the same
                        $isSameDate = $this->startDate === $this->endDate; // Adjust if necessary for your date format
                        $startDate = Carbon::parse($this->startDate)->startOfDay(); // Start of the selected start date
                        $endDate = Carbon::parse($this->endDate)->endOfDay(); // End of the selected end date

                        if ($isSameDate) {
                            // If the start date and end date are the same, only consider that specific day
                            $checkInCount = EmployeeAttendanceTimeIn::select(DB::raw('COUNT(DISTINCT DATE(check_in_time)) as unique_check_in_days'))
                                ->where('employee_id', $employeeId)
                                ->whereDate('check_in_time', $this->startDate)
                                ->first();
                        } else {
                            // If the start date and end date are different, consider the range
                            // $checkInCount = EmployeeAttendanceTimeIn::select(DB::raw('COUNT(DISTINCT DATE(check_in_time)) as unique_check_in_days'))
                            //     ->where('employee_id', $employeeId)
                            //     ->whereBetween('check_in_time', [$startDate, $endDate])
                            //     ->first();
                            $checkInCount = EmployeeAttendanceTimeIn::select(DB::raw('COUNT(DISTINCT DATE(employees_time_in_attendance.check_in_time)) as unique_check_in_days'))
                                        ->join('employees', 'employees_time_in_attendance.employee_id', '=', 'employees.id')
                                        ->join('working_hour', function($join) {
                                            $join->on('employees.department_id', '=', 'working_hour.department_id');
                                        })
                                        ->where('employees_time_in_attendance.employee_id', $employeeId)
                                        ->whereNotIn('employees_time_in_attendance.status', ['Absent', 'AWOL', 'On Leave'])
                                        ->whereNotIn('working_hour.day_of_week', [0, 6])
                                        ->whereBetween('check_in_time', [$startDate, $endDate]) // Exclude Saturday (6) and Sunday (7)
                                        ->first();
                                    

                        }

                        // Get the unique check-in days count
                        $uniqueCheckInDays = (int) $checkInCount->unique_check_in_days;
                        
                        // Calculate total hours to be rendered
                        $totalHoursTobeRendered = $totalHoursNeed * $uniqueCheckInDays;
                    } else {
                        
                        $employeeId = $attendance->employee_id; // Assuming you have this from $attendance
                        

                        $noww = new DateTime('now', new DateTimeZone('Asia/Taipei'));
                        $currentDatee = $noww->format('Y-m-d') . ' 00:00:00';

                        $checkInCount = EmployeeAttendanceTimeIn::select(DB::raw('COUNT(DISTINCT DATE(check_in_time)) as unique_check_in_days'))
                            ->where('employee_id', $employeeId)
                            ->where('check_in_time', '<>', $currentDatee)
                            ->whereNotIn('status', ['Absent', 'AWOL', 'On Leave'])
                            ->first();

                        $uniqueCheckInDays = (int) $checkInCount->unique_check_in_days;
                        $totalHoursTobeRendered = $totalHoursNeed * $uniqueCheckInDays;

                   
                    }
                         
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
                                $lateAM = $lateIntervalAM->h + ($lateIntervalAM->i / 60) + ($lateIntervalAM->s / 3600);

                                
                            }

 

                        }


                            $scheduledDiff = $morningStartTime->diff($morningEndTime);
                            $scheduledAMMinutes = ($scheduledDiff->h * 60) + $scheduledDiff->i + ($scheduledDiff->s / 60);

                                // Calculate actual worked time up to the morning end time including seconds
                                if ($effectiveCheckOutTime < $morningEndTime) {
                                    $actualDiff = $effectiveCheckOutTime->diff($morningStartTime);
                                } else {
                                    $actualDiff = $morningEndTime->diff($morningStartTime);
                                }
                                $actualMinutesUpToEnd = ($actualDiff->h * 60) + $actualDiff->i + ($actualDiff->s / 60);
                                 $undertimeAMTotal = max(0, $scheduledAMMinutes - $actualMinutesUpToEnd);
                                // Calculate undertime in minutes
                                $undertimeAM = max(0, $scheduledAMMinutes - $actualMinutesUpToEnd);
                
                    }  


                 

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
                                $latePM = $lateIntervalPM->h + ($lateIntervalPM->i / 60) + ($lateIntervalPM->s / 3600);
                            }

                            
                        }

                        

                        $scheduledPMDiff = $afternoonStartTime->diff($afternoonEndTime);
                        $scheduledPMMinutes = ($scheduledPMDiff->h * 60) + $scheduledPMDiff->i + ($scheduledPMDiff->s / 60);

                        // Calculate actual worked time up to the afternoon end time including seconds
                        if ($effectiveCheckOutTime < $afternoonEndTime) {
                            $actualPMDiff = $effectiveCheckOutTime->diff($afternoonStartTime);
                        } else {
                            $actualPMDiff = $afternoonEndTime->diff($afternoonStartTime);
                        }
                        $actualMinutesUpToEndPM = ($actualPMDiff->h * 60) + $actualPMDiff->i + ($actualPMDiff->s / 60);
                        $undertimePMTotal = max(0, $scheduledPMMinutes - $actualMinutesUpToEndPM);
                        // Calculate undertime in minutes
                        $undertimePM = max(0, $scheduledPMMinutes - $actualMinutesUpToEndPM);

                    }

                    // Calculate total hours worked
                    $totalHoursWorked = $hoursWorkedAM + $hoursWorkedPM;
                    
                    $totalHoursLate = $lateDurationAM + $lateDurationPM;
                    $totalUndertimeHours = $undertimeAM + $undertimePM;
                    $overallTotalHoursLate = $lateAM + $latePM;
                    $totalundertime = $undertimeAMTotal + $undertimePMTotal;

                    // $totalhoursNeed = $morningDuration + $afternoonDuration;
    
                    // Determine remark based on lateness
                    $remark = ($lateDurationAM > 0 || $lateDurationPM > 0) ? 'Late' : 'Present';

                    $modifyStatus = $attendance->status;

          

                    // Prepare the key for $attendanceData
                     $key = $attendance->employee_id . '-' . $checkInDate;

                    $employee_idd = $attendance->employee->employee_id;
                    $employee_id = $attendance->employee_id;
                    $employeeLastname = $attendance->employee->employee_lastname;
                    $employeeFirstname = $attendance->employee->employee_firstname;
                    $employeeMiddlename = $attendance->employee->employee_middlename;
                    $checkInTimer = $attendance->check_in_time;
                    // Check if this entry already exists in $attendanceData
                    if (isset($attendanceData[$key])) {
                        // Update existing entry
                        
                        $attendanceData[$key]->hours_perDay = $totalHoursNeedperDay;
                        $attendanceData[$key]->hours_workedAM += $hoursWorkedAM;
                        $attendanceData[$key]->hours_workedPM += $hoursWorkedPM;
                        $attendanceData[$key]->total_hours_worked += $totalHoursWorked;
                        $attendanceData[$key]->total_hours_late += $totalHoursLate;
                        $attendanceData[$key]->late_duration += $lateDurationAM;
                        $attendanceData[$key]->late_durationPM += $lateDurationPM;
                        $attendanceData[$key]->undertimeAM += $undertimeAM;
                        $attendanceData[$key]->undertimePM += $undertimePM;
                        $attendanceData[$key]->total_late += $totalHoursLate;
                        $attendanceData[$key]->remarks = $remark;
                        $attendanceData[$key]->modify_status = $modifyStatus;
                        $attendanceData[$key]->employee_idd = $employee_idd;
                        $attendanceData[$key]->employee_id = $employee_id;
                        $attendanceData[$key]->employee_lastname = $employeeLastname;
                        $attendanceData[$key]->employee_firstname = $employeeFirstname;
                        $attendanceData[$key]->employee_middlename = $employeeMiddlename;
                        $attendanceData[$key]->hours_late_overall += $overallTotalHoursLate;
                        $attendanceData[$key]->hours_undertime_overall += $totalundertime;
                        $attendanceData[$key]->check_in_time = $checkInTimer;


                        // dd($attendanceData[$key]->undertimeAM += $undertimeAM);
                    } else {
                        // Create new entry
                        $attendanceData[$key] = (object) [
                            'hours_perDay' => $totalHoursNeedperDay,
                            'employee_id' => $employee_id,
                            'employee_lastname' => $employeeLastname,
                            'employee_firstname' => $employeeFirstname, // Add employee_lastname
                            'employee_middlename' => $employeeMiddlename,
                            'worked_date' => $checkInDate,
                            'hours_workedAM' => $hoursWorkedAM,
                            'hours_workedPM' => $hoursWorkedPM,
                            'total_hours_worked' => $totalHoursWorked,
                            'total_hours_late' => $totalHoursLate,
                            'late_duration' => $lateDurationAM,
                            'late_durationPM' => $lateDurationPM,
                            'undertimeAM' => $undertimeAM,
                            'undertimePM' => $undertimePM,
                            'total_late' => $totalHoursLate,
                            'remarks' => $remark,
                            'modify_status'=> $modifyStatus,
                            'hours_late_overall' => $overallTotalHoursLate,
                            'hours_undertime_overall' => $totalundertime,
                            'check_in_time' => $checkInTimer,
                            'employee_idd' => $employee_idd,

                        ];

                        //  session()->put('late_duration', $lateDurationAM);
                    }

                    // Add total hours worked to overall total
                    $overallTotalHours += $totalHoursWorked;
                    $overallTotalLateHours += $overallTotalHoursLate;
                    $overallTotalUndertime += $totalundertime;
                    $overallTotalHoursSum = $overallTotalHoursSumm;
                }
            }
        }

        // Optionally, you can store the $attendanceData and $overallTotalHours in the session or pass it to a view
        session()->put('attendance_data', $attendanceData);

        session()->put('overall_total_hours', $overallTotalHours);

            // 'overallTotalHours' => $overallTotalHours,
            // 'overallTotalLateHours' => $overallTotalLateHours,
            // 'overallTotalUndertime' => $overallTotalUndertime,
            // 'totalHoursTobeRendered' => $totalHoursTobeRendered,
            // 'attendanceData' =>$attendanceData,
            // 'attendanceTimeIn' => $attendanceTimeIn,
            // 'attendanceTimeOut' => $attendanceTimeOut,
            // 'schools' => $schools,
            // 'departments' => $departments,
            // 'schoolToShow' => $this->schoolToShow,
            // 'departmentToShow' => $this->departmentToShow,
            // 'selectedEmployeeToShow' => $this->selectedEmployeeToShow,
            // 'employees' => $employees, // Ensure employees variable is defined if needed
            // 'selectedAttendanceByDate' => $this->selectedAttendanceByDate,
            // 'departmentDisplayWorkingHour' => $departmentDisplayWorkingHour,


                $pdf = \PDF::loadView('generate-pdf-for-payroll-departmental', [
                'overallTotalHours' => $overallTotalHours,
                'overallTotalLateHours' => $overallTotalLateHours,
                'overallTotalUndertime' => $overallTotalUndertime,
                'totalHoursTobeRendered' => $totalHoursTobeRendered,
                'selectedStartDate' => $this->startDate,
                'selectedEndDate' => $this->endDate,
                'attendanceData' => $attendanceData,
                'attendanceTimeIn' => $attendanceTimeIn,
                'attendanceTimeOut' => $attendanceTimeOut,
                'selectedEmployeeToShow' => $this->selectedEmployeeToShow,
                'departments' => $departments,
            ])->setPaper('letter', 'landscape'); // Set paper size and orientation

             $pdf->save($savePath . '/' . $filename);

            // Download the PDF file with the given filename
            return response()->download($savePath . '/' . $filename, $filename);
        } catch (\Exception $e) {
            // Log or handle the exception as needed
            dd($e->getMessage()); // Output the error for debugging
        }
    }
    
    public function generateExcel()
    {
        $departments = Department::where('id', $this->selectedDepartment4)->get();
        $department = Department::find($this->selectedDepartment4);

        try {
            if ($this->startDate && $this->endDate) {
                $selectedStartDate = date('jS F Y', strtotime($this->startDate));
                $selectedEndDate = date('jS F Y', strtotime($this->endDate));
                $dateRange = $selectedStartDate . ' to ' . $selectedEndDate;
            } else {
                $dateRange = 'No Date Selected';
            }

            if ($department) {
                $departmentName = $department->department_abbreviation;
                $filename = $departmentName . ' - ' . $dateRange . '.xlsx';
            } else {
                $filename = 'Unknown Department - ' . $dateRange . '.xlsx';
            }

            $queryTimeIn = EmployeeAttendanceTimeIn::query()->with(['employee.school', 'employee.department']);
            $queryTimeOut = EmployeeAttendanceTimeOut::query()->with(['employee.school', 'employee.department']);

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

            if ($this->startDate && $this->endDate) {
                $queryTimeIn->whereDate('check_in_time', '>=', $this->startDate)
                            ->whereDate('check_in_time', '<=', $this->endDate);
                $queryTimeOut->whereDate('check_out_time', '>=', $this->startDate)
                            ->whereDate('check_out_time', '<=', $this->endDate);
            }

            $attendanceTimeIn = $queryTimeIn->orderBy('employee_id', 'asc')->get();
            $attendanceTimeOut = $queryTimeOut->orderBy('employee_id', 'asc')->get();

                

            $export = new AttendanceExport($attendanceTimeIn, $attendanceTimeOut);

            return Excel::download($export, $filename);

        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while generating the Excel file: ' . $e->getMessage());
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