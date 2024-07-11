<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\Admin\School; 
use \App\Models\Admin\Employee; 

class EmployeeAttendance extends Model
{
    use HasFactory;
    protected $table = 'employee_attendance';

    protected $fillable = [
        'employee_id', //FK
        'date_of_atttendance',
        'check_in_time',
        'check_out_time',
        'status',
        'notes',
        'shift_start',
        'shift_end',
        'overtime_hours',
        'last_tap_time',

    ];

    // //each employee attenandance record belongs to one school
    // public function school()
    // {
    //     return $this->belongsTo(School::class);
    // }

    public function school()
    {
        return $this->employee->school();
    }

    public function department()
    {
        return $this->employee->department();
    }

    // Each attendance record belongs to one employee
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
