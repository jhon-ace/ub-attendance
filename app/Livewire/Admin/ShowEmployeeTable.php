<?php

namespace App\Livewire\Admin;

use \App\Models\Admin\Employee; 
use \App\Models\Admin\School; 
use \App\Models\Admin\Department; 
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;

class ShowEmployeeTable extends Component
{
    use WithPagination; 
    public $search = '';
    public $sortField = 'school_id';
    public $sortDirection = 'asc';
    public $selectedSchool = null;
    public $selectedDepartment = null;
    public $departmentsToShow;
    public $schoolToShow;
    public $departmentToShow;

    protected $listeners = ['updateEmployees', 'updateEmployeesByDepartment'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->departmentsToShow = collect([]); // Initialize as an empty collection
        $this->schoolToShow = collect([]); // Initialize as an empty collection
        $this->departmentToShow = collect([]);
    }

    public function updatingSelectedSchool()
    {
        $this->resetPage();
        $this->updateEmployees();
    }

    public function updatingSelectedDepartment()
    {
        $this->resetPage();
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
        $query = Employee::with('school');

        // Apply search filters
        $query = $this->applySearchFilters($query);

        // Apply selected school filter
        if ($this->selectedSchool) {
            $query->where('school_id', $this->selectedSchool);
            $this->schoolToShow = School::find($this->selectedSchool);
        } else {
            $this->schoolToShow = null; // Reset schoolToShow if no school is selected
        }

        // Apply selected department filter
        if ($this->selectedDepartment) {
            $query->where('department_id', $this->selectedDepartment);
            $this->departmentToShow = Department::find($this->selectedDepartment);
        } else {
            $this->departmentToShow = null; // Reset departmentToShow if no department is selected
        }

        $employees = $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        $schools = School::all();
        $departments = Department::where('school_id', $this->selectedSchool)->get();

        $departmentCounts = Department::select('school_id', \DB::raw('count(*) as department_count'))
            ->groupBy('school_id')
            ->get()
            ->keyBy('school_id');

        return view('livewire.admin.show-employee-table', [
            'employees' => $employees,
            'schools' => $schools,
            'departments' => $departments,
            'departmentCounts' => $departmentCounts,
        ]);
    }

    public function updateEmployees()
    {
        // Update departmentsToShow based on selected school
        if ($this->selectedSchool) {
            $this->departmentsToShow = Department::where('school_id', $this->selectedSchool)->get();
        } else {
            $this->departmentsToShow = collect(); // Reset to empty collection if no school is selected
        }

        // Ensure departmentToShow is reset if the selected school changes
        $this->selectedDepartment = null;
        $this->departmentToShow = null;
    }

    public function updateEmployeesByDepartment()
    {
        // Update departmentToShow based on selected department
        if ($this->selectedDepartment) {
            $this->departmentToShow = Department::find($this->selectedDepartment);
        } else {
            $this->departmentToShow = collect(); // Reset to empty collection if no department is selected
        }
    }

    protected function applySearchFilters($query)
    {
        return $query->where(function (Builder $query) {
            $query->where('employee_id', 'like', '%' . $this->search . '%')
                ->orWhere('employee_firstname', 'like', '%' . $this->search . '%')
                ->orWhere('employee_middlename', 'like', '%' . $this->search . '%')
                ->orWhere('employee_lastname', 'like', '%' . $this->search . '%')
                ->orWhere('employee_rfid', 'like', '%' . $this->search . '%')
                ->orWhereHas('school', function (Builder $query) {
                    $query->where('abbreviation', 'like', '%' . $this->search . '%')
                        ->orWhere('school_name', 'like', '%' . $this->search . '%');
                });
        });
    }
    
}
