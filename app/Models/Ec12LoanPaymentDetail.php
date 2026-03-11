<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ec12LoanPaymentDetail extends Model
{
    use HasFactory;
    protected $table = 'ec12_loan_payment_details';
    protected $guarded = ['id'];
    protected $fillable = [
        'loan_payment_id',
        'loan_emi_schedule_id',
        'loan_assign_detail_amount',
        'is_active',
        'remarks',
    ];

    public function loanPayment()
    {
        return $this->belongsTo(Ec11LoanPayment::class, 'loan_payment_id');
    }

    public function emiSchedule()
    {
        return $this->belongsTo(Ec07LoanEmiSchedule::class, 'loan_emi_schedule_id');
    }
}
