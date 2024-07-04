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


    public function updatingSearch()
    {
        $this->resetPage();
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
        $employees = Employee::with(['school', 'department'])
                    ->where(function (Builder $query) {
                        $query->where('employee_id', 'like', '%' . $this->search . '%')
                            ->orWhere('employee_firstname', 'like', '%' . $this->search . '%')
                            ->orWhere('employee_middlename', 'like', '%' . $this->search . '%')
                            ->orWhere('employee_lastname', 'like', '%' . $this->search . '%')
                            ->orWhere('employee_rfid', 'like', '%' . $this->search . '%')
                            ->orWhereHas('school', function (Builder $query) {
                                $query->where('abbreviation', 'like', '%' . $this->search . '%')
                                        ->orWhere('school_name', 'like', '%' . $this->search . '%');
                            });
                    })
                    ->orderBy($this->sortField, $this->sortDirection)
                    ->paginate(10);

                     // Apply school filter if selectedSchool is not null
                    // if ($this->selectedSchool) {
                    //     $query->where('school_id', $this->selectedSchool);
                    // }

                    // $employees = $query->orderBy($this->sortField, $this->sortDirection)
                    //                 ->paginate(10);

                    // Fetch all schools and departments
            $schools = School::all();
            $departments = Department::where('school_id', $this->selectedSchool)->get();

        return view('livewire.admin.show-employee-table', [
            'employees' => $employees,
            'schools' => $schools,
            'departments' => $departments,
        ]);
    }

    public function updateDepartments()
    {
        $this->departments = Department::where('school_id', $this->selectedSchool)->get();
    }
    
}
