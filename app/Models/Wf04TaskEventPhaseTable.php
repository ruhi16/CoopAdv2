<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wf04TaskEventPhaseTable extends Model
{
    use HasFactory;
    protected $table = 'wf04_task_event_phase_tables';
    protected $guarded = ['id'];
    protected $fillable = [
        'name',
        'description',
        'model_name',
        'school_id',
        'is_active',
        'remarks',
    ];
}
