<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Financialyear extends Model
{
    use HasFactory;
    protected $table = 'financialyears';
    protected $guarded = ['id'];
    protected $fillable = [
        'name',
        'description',
        'order_index',
        'start_date',
        'end_date',
        'is_default',
        'is_active',
        'created_by',
        'approved_by',
        'school_id',
        'remarks',
        'status',
    ];
}
