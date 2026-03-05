<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberType extends Model
{
    use HasFactory;
    protected $table = 'member_types';
    protected $guarded = ['id'];
    protected $fillable = [
        'name',
        'description',
        'order_index',
        'join_date',
        'exit_date',
        'is_default',
        'is_active',
        'created_by',
        'approved_by',
        'school_id',
        'remarks',
        'status',
    ];

    public function memberDbs()
    {
        return $this->hasMany(MemberDb::class, 'member_type_id');
    }
}
