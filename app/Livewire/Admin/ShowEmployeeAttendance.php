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

        // Calculate hours worked for each attendance record in $attendanceTimeIn
        foreach ($attendanceTimeIn as $attendance) {
            // Find corresponding check_out_time
            $checkOut = $attendanceTimeOut->where('employee_id', $attendance->employee_id)
                                        ->where('check_out_time', '>=', $attendance->check_in_time)
                                        ->first();

            if ($checkOut) {
                // Extract dates from check_in_time and check_out_time
                $checkInDate = date('m-d-Y', strtotime($attendance->check_in_time));
                $checkOutDate = date('m-d-Y', strtotime($checkOut->check_out_time));

                // Check if dates match
                if ($checkInDate === $checkOutDate) {
                    // Calculate hours worked
                    $checkIn = strtotime($attendance->check_in_time);
                    $checkOutTime = strtotime($checkOut->check_out_time);
                    $hoursWorked = ($checkOutTime - $checkIn) / 3600; // Calculate hours difference

                    // Round to two decimal places
                    $attendance->hours_worked = round($hoursWorked, 2);
                    $attendance->worked_date = $checkInDate; // Store the worked date
                    $attendance->remarks = 'Present'; // No remark needed if hours worked is recorded
                } else {
                    // Dates do not match, mark as absent
                    $attendance->hours_worked = 0; // Mark as absent
                    $attendance->worked_date = $checkInDate; // Use check_in_time date as worked date
                    $attendance->remarks = 'Absent'; // Add remark for absence
                }
            } else {
                // No check_out_time found, mark as absent
                $attendance->hours_worked = 0; // Mark as absent
                $attendance->worked_date = date('m-d-Y', strtotime($attendance->check_in_time)); // Use check_in_time date as worked date
                $attendance->remarks = 'Absent'; // Add remark for absence
            }
        }




        $schools = School::all();
        $departments = Department::where('school_id', $this->selectedSchool)
            ->where('dept_identifier', 'employee')
            ->get();

        return view('livewire.admin.show-employee-attendance', [
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
        try {
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


            // Calculate hours worked for each attendance record in $attendanceTimeIn
            foreach ($attendanceTimeIn as $attendance) {
                // Find corresponding check_out_time
                $checkOut = $attendanceTimeOut->where('employee_id', $attendance->employee_id)
                                            ->where('check_out_time', '>=', $attendance->check_in_time)
                                            ->first();

                if ($checkOut) {
                    // Extract dates from check_in_time and check_out_time
                    $checkInDate = date('m-d-Y', strtotime($attendance->check_in_time));
                    $checkOutDate = date('m-d-Y', strtotime($checkOut->check_out_time));

                    // Check if dates match
                    if ($checkInDate === $checkOutDate) {
                        // Calculate hours worked
                        $checkIn = strtotime($attendance->check_in_time);
                        $checkOutTime = strtotime($checkOut->check_out_time);
                        $hoursWorked = ($checkOutTime - $checkIn) / 3600; // Calculate hours difference

                        // Round to two decimal places
                        $attendance->hours_worked = round($hoursWorked, 2);
                        $attendance->worked_date = $checkInDate; // Store the worked date
                        $attendance->remarks = 'Present'; // No remark needed if hours worked is recorded
                    } else {
                        // Dates do not match, mark as absent
                        $attendance->hours_worked = 0; // Mark as absent
                        $attendance->worked_date = $checkInDate; // Use check_in_time date as worked date
                        $attendance->remarks = 'Absent'; // Add remark for absence
                    }
                } else {
                    // No check_out_time found, mark as absent
                    $attendance->hours_worked = 0; // Mark as absent
                    $attendance->worked_date = date('m-d-Y', strtotime($attendance->check_in_time)); // Use check_in_time date as worked date
                    $attendance->remarks = 'Absent'; // Add remark for absence
                }
            }

            // Generate PDF using the 'generate-pdf' view and data
            $pdf = \PDF::loadView('generate-pdf', [
                'attendanceTimeIn' => $attendanceTimeIn,
                'attendanceTimeOut' => $attendanceTimeOut,
                'selectedEmployeeToShow' => $this->selectedEmployeeToShow,
            ])->setPaper('a4', 'portrait'); // Set paper size and orientation


            // Download the PDF file with the given filename
            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->stream();
                }, 'name.pdf');
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
