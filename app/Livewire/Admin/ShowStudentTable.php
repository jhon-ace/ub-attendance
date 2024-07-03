<?php

namespace App\Livewire\Admin;


use \App\Models\Admin\Student; 
use \App\Models\Admin\School; 
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;

class ShowStudentTable extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'school_id';
    public $sortDirection = 'desc';

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

        $students = Student::with('school')
            ->where(function (Builder $query) {
                $query->where('student_id', 'like', '%' . $this->search . '%')
                      ->orWhere('student_firstname', 'like', '%' . $this->search . '%')
                      ->orWhere('student_middlename', 'like', '%' . $this->search . '%')
                      ->orWhere('student_lastname', 'like', '%' . $this->search . '%')
                      ->orWhere('student_rfid', 'like', '%' . $this->search . '%')
                      ->orWhereHas('school', function (Builder $query) {
                          $query->where('abbreviation', 'like', '%' . $this->search . '%')
                          ->orWhere('school_name', 'like', '%' . $this->search . '%');
                      });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

            $schools = School::all();

        return view('livewire.admin.show-student-table', [
            'students' => $students,
            'schools' => $schools,
        ]);
    
    }
}
