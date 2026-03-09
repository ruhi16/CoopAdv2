<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ec21BankLoanScheme extends Model
{
    use HasFactory;
    protected $table = 'ec21_bank_loan_schemes';
    protected $guarded = ['id'];
    
    protected $casts = [
        'effected_on' => 'date',
        'finalized_at' => 'date',
        'is_finalized' => 'boolean',
        'is_active' => 'boolean',
    ];
    
    /**
     * Get the bank that owns the loan scheme
     */
    public function bank()
    {
        return $this->belongsTo(Ec20Bank::class, 'bank_id');
    }
    
    /**
     * Get the user who created the loan scheme
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * Get the organisation associated with the loan scheme
     */
    public function organisation()
    {
        return $this->belongsTo(Ec01Organisation::class, 'organisation_id');
    }
    
    /**
     * Get the financial year associated with the loan scheme
     */
    public function financialYear()
    {
        return $this->belongsTo(Ec02FinancialYear::class, 'financial_year_id');
    }
    
    /**
     * Get the user who finalized the loan scheme
     */
    public function finalizedBy()
    {
        return $this->belongsTo(User::class, 'finalized_by');
    }
}