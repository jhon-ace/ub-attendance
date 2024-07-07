<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\Admin\School; 
use \App\Models\Admin\Employee; 

class Department extends Model
{
    use HasFactory;
    protected $table = 'departments';
    protected $primaryKey = 'department_id';
     public $incrementing = false; 

    protected $fillable = [
        'department_id',
        'school_id',
        'department_abbreviation',
        'department_name',
    ];



    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    // A department has many employees
    public function employees()
    {
        return $this->hasMany(Employee::class, 'department_id', 'department_id');
    }



    // each department belongs to one school
    // public function school()
    // {
    //     return $this->belongsTo(School::class, 'department_id');
    // }

    // // A department has many Employee
    // public function employees()
    // {
    //     return $this->hasMany(Employee::class);
    // }
}
