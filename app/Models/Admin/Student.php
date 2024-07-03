<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use \App\Models\Admin\School; 

class Student extends Model
{
    use HasFactory, HasRoles;
    protected $table = 'students';

    protected $fillable = [
        'school_id',
        'student_id',
        'student_firstname',
        'student_middlename',
        'student_lastname',
        'student_rfid',
    ];

    //each student belongs to one school
    public function school()
    {
        return $this->belongsTo(School::class);
    }
    
}
