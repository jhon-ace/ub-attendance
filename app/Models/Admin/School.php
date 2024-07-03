<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use \App\Models\Admin\Staff; 

class School extends Model
{
    use HasFactory, HasRoles;

    protected $table = 'schools';

    protected $fillable = [
        'abbreviation',
        'school_name',
    ];

    // A school has many staff
    public function staff()
    {
        return $this->hasMany(Staff::class);
    }
}
