<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanPaymentParticular extends Model
{
    use HasFactory;
    protected $table = 'loan_payment_particulars';
    protected $guarded = ['id'];
    protected $fillable = [
        'ec01_loan_scheme_id',
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
