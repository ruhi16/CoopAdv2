<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wf04TaskEventPhaseTableOperation extends Model
{
    use HasFactory;
    protected $table = 'wf04_task_event_phase_table_operations';
    protected $guarded = ['id'];
    protected $fillable = [
        'name',
        'description',
        'task_event_phase_table_id',
        'school_id',
        'is_active',
        'remarks',
    ];

    public function taskEventPhaseTable()
    {
        return $this->belongsTo(Wf04TaskEventPhaseTable::class, 'task_event_phase_table_id');
    }
}
