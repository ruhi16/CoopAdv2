<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolOfficial extends Model
{
    use HasFactory;
    protected $table = 'school_officials';
    protected $guarded = ['id'];
}
