<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use \App\Models\Admin\School; 

class Employee extends Model
{
    use HasFactory;
    protected $table = 'employees';

    protected $fillable = [
        'school_id',
        'employee_id',
        'employee_firstname',
        'employee_middlename',
        'employee_lastname',
        'employee_rfid',
    ];

    //each employee belongs to one school
    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
