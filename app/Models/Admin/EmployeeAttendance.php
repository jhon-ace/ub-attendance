<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\Admin\School; 
use \App\Models\Admin\Employee; 

class EmployeeAttendance extends Model
{
    use HasFactory;
    protected $table = '';

    protected $fillable = [
        'employee_id',
        'school_id',
        'date',
        'check_in_time',
        'check_out_time',
        'status',
        'notes',
        'shift_start',
        'shift_end',
        'overtime_hours',
        'last_tap_time',

    ];

    //each employee attenandance record belongs to one school
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    // Each attendance record belongs to one employee
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
