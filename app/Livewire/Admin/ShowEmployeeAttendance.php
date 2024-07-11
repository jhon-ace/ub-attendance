<?php

namespace App\Livewire\Admin;

use App\Models\Admin\EmployeeAttendance;
use App\Models\Admin\School;
use App\Models\Admin\Department;
use App\Models\Admin\Employee;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;

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

    protected $listeners = ['updateEmployees', 'updateEmployeesByDepartment', 'updateAttendanceByEmployee'];

    public function updatingSearch()
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
        $this->updateEmployeesByDepartment();
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
        $query = EmployeeAttendance::query()->with(['employee.school', 'employee.department']);

        // // Apply search filters
        // $query = $this->applySearchFilters($query);

        // Apply selected school filter
        if ($this->selectedSchool) {
            $query->whereHas('employee', function (Builder $query) {
                $query->where('school_id', $this->selectedSchool);
            });
            $this->schoolToShow = School::find($this->selectedSchool);
        } else {
            $this->schoolToShow = null;
        }

        // Apply selected department filter
        if ($this->selectedDepartment) {
            $query->whereHas('employee', function (Builder $query) {
                $query->where('department_id', $this->selectedDepartment);
            });
            $this->departmentToShow = Department::find($this->selectedDepartment);

            // Fetch courses for the selected department
            $employees = Employee::where('department_id', $this->selectedDepartment)->get();
        } else {
            $this->departmentToShow = null;
            $employees = Employee::all(); // Fetch all courses if no department selected
        }

        // Apply selected course filter
        if ($this->selectedEmployee) {
            $query->where('employee_id', $this->selectedEmployee);
            $this->selectedEmployeeToShow = Employee::find($this->selectedEmployee);
        } else {
            $this->selectedEmployeeToShow = null;
        }

        $attendances = $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        $schools = School::all();
        $departments = Department::where('school_id', $this->selectedSchool)
            ->where('dept_identifier', 'employee')
            ->get();

        $studentsCounts = EmployeeAttendance::select('employee_id', \DB::raw('count(*) as student_count'))
            ->groupBy('employee_id')
            ->get()
            ->keyBy('employee_id');

        return view('livewire.admin.show-employee-attendance', [
            'attendances' => $attendances,
            'schools' => $schools,
            'departments' => $departments,
            'studentsCounts' => $studentsCounts,
            'schoolToShow' => $this->schoolToShow,
            'departmentToShow' => $this->departmentToShow,
            'selectedEmployeeToShow' => $this->selectedEmployeeToShow,
            'employees' => $employees, // Pass the courses to the view
        ]);
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
    }

    public function updateEmployeesByDepartment()
    {
        if ($this->selectedDepartment && $this->selectedSchool) {
            $this->departmentToShow = Department::where('id', $this->selectedDepartment)
                ->where('school_id', $this->selectedSchool)
                ->first();
        } else {
            $this->departmentToShow = null;
        }
    }

    public function updateAttendanceByEmployee()
    {
        if ($this->selectedEmployee) {
            $this->attendancesToShow = EmployeeAttendance::where('employee_id', $this->selectedEmployee)->get();
        } else {
            $this->attendancesToShow = collect();
        }
    }

    // protected function applySearchFilters($query)
    // {
    //     return $query->where(function (Builder $query) {
    //         $query->where('student_id', 'like', '%' . $this->search . '%')
    //             ->orWhere('student_lastname', 'like', '%' . $this->search . '%')
    //             ->orWhere('student_firstname', 'like', '%' . $this->search . '%')
    //             ->orWhere('student_middlename', 'like', '%' . $this->search . '%')
    //             ->orWhere('student_year_grade', 'like', '%' . $this->search . '%')
    //             ->orWhere('student_rfid', 'like', '%' . $this->search . '%')
    //             ->orWhere('student_status', 'like', '%' . $this->search . '%')
    //             ->orWhereHas('course', function (Builder $query) {
    //                 $query->where('course_abbreviation', 'like', '%' . $this->search . '%')
    //                     ->orWhere('course_name', 'like', '%' . $this->search . '%');
    //             })
    //             ->orWhereHas('course.department', function (Builder $query) {
    //                 $query->where('department_abbreviation', 'like', '%' . $this->search . '%')
    //                     ->orWhere('department_name', 'like', '%' . $this->search . '%');
    //             });
    //     });
    // }
}
