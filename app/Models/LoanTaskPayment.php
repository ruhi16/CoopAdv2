<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanTaskPayment extends Model
{
    use HasFactory;
    protected $table = 'loan_task_payments';
    protected $guarded = ['id'];
    protected $fillable = [
        'ec01_loan_scheme_id',
        'ec03_loan_request_id',
        'ec05_loan_assign_id',
        'ec06_loan_assign_detail_id',
        'ec07_loan_emi_schedule_id',
        'loan_payment_particular_id',
        'loan_payment_particular_total_id',
        'amount',
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
