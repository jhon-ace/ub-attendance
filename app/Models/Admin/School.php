<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class School extends Model
{
    use HasFactory, HasRoles;

    protected $table = 'schools';

    protected $fillable = [
        'abbreviation',
        'school_name',
    ];

}
