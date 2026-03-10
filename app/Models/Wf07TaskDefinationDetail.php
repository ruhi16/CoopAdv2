<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wf07TaskDefinationDetail extends Model
{
    use HasFactory;
    protected $table = 'wf07_task_defination_details';
    protected $guarded = ['id'];
    protected $fillable = [
        'name',
        'description',
        'task_defination_id',
        'task_event_sequence_no',
        'task_event_phase_id',
        'task_event_phase_table_id',
        'task_event_phase_table_operation_id',
        'school_id',
        'is_active',
        'created_by',
        'approved_by',
        'remarks',
    ];

    public function taskDefination()
    {
        return $this->belongsTo(Wf07TaskDefination::class, 'task_defination_id');
    }

    public function taskEventPhase()
    {
        return $this->belongsTo(Wf03TaskEventPhase::class, 'task_event_phase_id');
    }

    public function taskEventPhaseTable()
    {
        return $this->belongsTo(Wf04TaskEventPhaseTable::class, 'task_event_phase_table_id');
    }

    public function taskEventPhaseTableOperation()
    {
        return $this->belongsTo(Wf04TaskEventPhaseTableOperation::class, 'task_event_phase_table_operation_id');
    }
}
