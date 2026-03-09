<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ec21BankLoanBorrowed extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'ec21_bank_loan_borroweds';
    
    protected static function boot()
    {
        parent::boot();
        
        static::deleting(function ($model) {
            // Delete related specifications when the main record is deleted
            $model->specifications()->delete();
        });
    }
    
    public function loanScheme(): BelongsTo
    {
        return $this->belongsTo(Ec21BankLoanScheme::class, 'bank_loan_scheme_particular_id');
    }
    
    public function loanSchemeDetail(): BelongsTo
    {
        return $this->belongsTo(Ec21BankLoanScheme::class, 'bank_loan_scheme_particular_id');
    }
    
    public function specifications(): HasMany
    {
        return $this->hasMany(Ec21BankLoanBorrowedSpec::class, 'bank_loan_borrowed_id');
    }
    
    // Note: This model doesn't have a direct bank relationship since the bank comes from the loan scheme.
    // We can get the bank through the loan scheme relationship.
}