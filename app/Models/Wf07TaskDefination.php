<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wf07TaskDefination extends Model
{
    use HasFactory;
    protected $table = 'wf07_task_definations';
    protected $guarded = ['id'];
    protected $fillable = [
        'name',
        'description',
        'task_category_id',
        'task_event_id',
        'created_by',
        'approved_by',
        'school_id',
        'is_active',
        'remarks',
    ];

    public function taskCategory()
    {
        return $this->belongsTo(Wf01TaskCategory::class, 'task_category_id');
    }

    public function taskEvent()
    {
        return $this->belongsTo(Wf02TaskEvent::class, 'task_event_id');
    }
}
