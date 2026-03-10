<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wf03TaskEventPhase extends Model
{
    use HasFactory;
    protected $table = 'wf03_task_event_phases';
    protected $guarded = ['id'];
    protected $fillable = [
        'name',
        'description',
        'school_id',
        'is_active',
        'remarks',
    ];
}
