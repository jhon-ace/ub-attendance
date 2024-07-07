<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use \App\Models\Admin\School; 
use \App\Models\Admin\Employee; 

class Employee extends Model
{
    use HasFactory;
    protected $table = 'employees';
    protected $primaryKey = 'employee_id';


    protected $fillable = [
        'employee_id',
        'school_id',
        'department_id',
        'employee_firstname',
        'employee_middlename',
        'employee_lastname',
        'employee_rfid',
    ];


    // Each employee belongs to one school
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    // Each employee belongs to one department
    public function department()
    {
        return $this->belongsTo(Department::class,  'department_id');
    }

    // Each employee can have many attendance records
    public function attendance()
    {
        return $this->hasMany(EmployeeAttendance::class);
    }

public static function employeesByDepartment()
    {
        return self::with('department')->get()->groupBy('department_id');
    }






    // //each employee belongs to one school
    // public function school()
    // {
    //     return $this->belongsTo(School::class, 'department_id');
    // }

    // // Each employee belongs to one departments
    // public function department()
    // {
    //     return $this->belongsTo(Department::class, 'department_id', 'department_id');
    // }

    //  // Each employee can have many attendance records
    // public function attendance()
    // {
    //     return $this->hasMany(EmployeeAttendance::class);
    // }
}
