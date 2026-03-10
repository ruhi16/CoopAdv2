<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wf02TaskEvent extends Model
{
    use HasFactory;
    protected $table = 'wf02_task_events';
    protected $guarded = ['id'];
    protected $fillable = [
        'task_category_id',
        'name',
        'description',
        'school_id',
        'is_active',
        'remarks',
    ];

    public function taskCategory()
    {
        return $this->belongsTo(Wf01TaskCategory::class, 'task_category_id');
    }
}
