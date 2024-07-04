<?php

namespace App\Livewire\Admin;

use \App\Models\Admin\School; 
use \App\Models\Admin\Department; 
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;

class ShowDepartmentTable extends Component
{
    
    use WithPagination;

    public $search = '';
    public $sortField = 'school_id';
    public $sortDirection = 'asc';
    public $selectedSchool = null;
    public $selectedDepartment = null;
    public $departmentsToShow;
    public $schoolToShow;

    protected $listeners = ['updateDepartments'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->departmentsToShow = collect([]); // Initialize as an empty collection
        $this->schoolToShow = collect([]); // Initialize as an empty collection
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
             $query = Department::with('school')
                ->where(function (Builder $query) {
                    $query->where('department_id', 'like', '%' . $this->search . '%')
                        ->orWhere('department_abbreviation', 'like', '%' . $this->search . '%')
                        ->orWhere('department_name', 'like', '%' . $this->search . '%')
                        ->orWhereHas('school', function (Builder $query) {
                            $query->where('abbreviation', 'like', '%' . $this->search . '%')
                                ->orWhere('school_name', 'like', '%' . $this->search . '%');
                        });

                    if ($this->selectedSchool) {
                        $query->where('school_id', $this->selectedSchool);
                    }

                    // Add condition to filter by selected department
                    if ($this->selectedDepartment) {
                        $query->where('id', $this->selectedDepartment);
                    }
                });

            $departments = $query->orderBy($this->sortField, $this->sortDirection)
                ->paginate(10);

            $schools = School::all();

        return view('livewire.admin.show-department-table', [
            'departments' => $departments,
            'schools' => $schools,
            // 'selectedSchool' => $selectedSchool,
        ]);
}


    public function updateDepartments()
    {
        if ($this->selectedSchool) {
            $this->departmentsToShow = Department::where('school_id', $this->selectedSchool)->get(); // Ensure this returns a collection
            $this->schoolToShow = School::where('id', $this->selectedSchool)->get();
        } else {
            $this->departmentsToShow = collect(); // Reset to empty collection if no school is selected
            $this->schoolToShow = collect(); // Reset to empty collection if no school is selected
        }
    }
    
}
