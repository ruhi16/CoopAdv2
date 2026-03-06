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
        'loan_assign_id',
        'name',
        'description',
        'order_index',
        'emi_schedule_index',
        'emi_due_date',
        'emi_paid_date',
        'total_emi_amount',
        'principal_emi_amount',
        'interest_emi_amount',
        'principal_balance_amount_before_emi',
        'principal_balance_amount_after_emi',
        'is_default',
        'is_active',
        'created_by',
        'approved_by',
        'school_id',
        'remarks',
        'status',
    ];

    public function loanAssign()
    {
        return $this->belongsTo(Ec05LoanAssign::class, 'loan_assign_id');
    }
}
