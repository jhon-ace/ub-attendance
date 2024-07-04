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

        $departments = Department::with('school')
            ->where(function (Builder $query) {
                $query->where('department_id', 'like', '%' . $this->search . '%')
                      ->orWhere('department_abbreviation', 'like', '%' . $this->search . '%')
                      ->orWhere('department_name', 'like', '%' . $this->search . '%')
                      ->orWhereHas('school', function (Builder $query) {
                          $query->where('abbreviation', 'like', '%' . $this->search . '%')
                          ->orWhere('school_name', 'like', '%' . $this->search . '%');
                      });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

            $schools = School::all();

        return view('livewire.admin.show-department-table', [
            'departments' => $departments,
            'schools' => $schools,
        ]);
    }
    
}
