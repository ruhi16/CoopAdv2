<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wf04TaskEventPhaseTableOperationStatus extends Model
{
    use HasFactory;
    protected $table = 'wf04_task_event_phase_table_operation_statuses';
    protected $guarded = ['id'];
    protected $fillable = [
        'name',
        'description',
        'school_id',
        'is_active',
        'remarks',
    ];
}
