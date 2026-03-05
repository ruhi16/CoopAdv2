<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ec07LoanEmiSchedule extends Model
{
    use HasFactory;
    protected $table = 'ec07_loan_emi_schedules';
    protected $guarded = ['id'];
    protected $fillable = [
        'ec03_loan_request_id',
        'ec05_loan_assign_id',
        'ec06_loan_assign_detail_id',
        'name',
        'description',      
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
}
