<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;
    protected $table = 'members';
    protected $guarded = ['id'];
    protected $fillable = [
        'member_db_id',
        'name',
        'description',
        'order_index',
        'join_date',
        'exit_date',
        'financial_year',
        'is_default',
        'is_active',
        'created_by',
        'approved_by',
        'school_id',
        'remarks',
        'status',
    ];

    public function memberDb()
    {
        return $this->belongsTo(MemberDb::class, 'member_db_id');
    }
}
