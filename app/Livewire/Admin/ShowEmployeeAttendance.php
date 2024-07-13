<?php

namespace App\Livewire\Admin;

use App\Models\Admin\EmployeeAttendanceTimeIn;
use App\Models\Admin\EmployeeAttendanceTimeOut;
use App\Models\Admin\School;
use App\Models\Admin\Department;
use App\Models\Admin\Employee;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Carbon\Carbon;



class ShowEmployeeAttendance extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'employee_id';
    public $sortDirection = 'asc';
    public $selectedSchool = null;
    public $selectedDepartment = null;
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
        if ($this->selectedDepartment) {
            $queryTimeIn->whereHas('employee', function (Builder $query) {
                $query->where('department_id', $this->selectedDepartment);
            });
            $queryTimeOut->whereHas('employee', function (Builder $query) {
                $query->where('department_id', $this->selectedDepartment);
            });
            $this->departmentToShow = Department::find($this->selectedDepartment);
            $employees = Employee::where('department_id', $this->selectedDepartment)->get();
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

// Now $overallTotalHours contains the sum of all total hours worked



        $schools = School::all();
        $departments = Department::where('school_id', $this->selectedSchool)
            ->where('dept_identifier', 'employee')
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
            if ($this->selectedDepartment) {
                $queryTimeIn->whereHas('employee', function (Builder $query) {
                    $query->where('department_id', $this->selectedDepartment);
                });
                $queryTimeOut->whereHas('employee', function (Builder $query) {
                    $query->where('department_id', $this->selectedDepartment);
                });
                $this->departmentToShow = Department::find($this->selectedDepartment);
                $employees = Employee::where('department_id', $this->selectedDepartment)->get();
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

        $this->selectedDepartment = null;
        $this->departmentToShow = null;
        $this->startDate = null; // Reset start date
        $this->endDate = null; // Reset end date
    }

    public function updateEmployeesByDepartment()
    {
        if ($this->selectedDepartment && $this->selectedSchool) {
            $this->departmentToShow = Department::where('id', $this->selectedDepartment)
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
