<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ec11LoanPayment extends Model
{
    use HasFactory;
    protected $table = 'ec11_loan_payments';
    protected $guarded = ['id'];
    protected $fillable = [
        'loan_assign_id',
        'member_id',
        'task_execution_id',
        'task_execution_detail_id',
        'payment_total_amount',
        'payment_date',
        'payment_method',
        'is_paid',
        'principal_balance_amount_before_payment',
        'principal_balance_amount_after_payment',
        'financial_year_id',
        'is_active',
        'remarks',
    ];

    public function loanAssign()
    {
        return $this->belongsTo(Ec05LoanAssign::class, 'loan_assign_id');
    }

    public function member()
    {
        return $this->belongsTo(MemberDb::class, 'member_id');
    }

    public function paymentDetails()
    {
        return $this->hasMany(Ec12LoanPaymentDetail::class, 'loan_payment_id');
    }
}
