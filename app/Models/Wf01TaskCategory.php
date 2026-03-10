<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wf01TaskCategory extends Model
{
    use HasFactory;
    protected $table = 'wf01_task_categories';
    protected $guarded = ['id'];
    protected $fillable = [
        'name',
        'description',
        'school_id',
        'is_active',
        'remarks',
    ];
}
