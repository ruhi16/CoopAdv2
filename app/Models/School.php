<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;
    protected $table = 'schools';
    protected $guarded = ['id'];

    protected $fillable = [
        'name',
        'code',
        'address',
        'phone',
        'email',
    ];
}
