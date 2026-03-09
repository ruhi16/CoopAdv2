<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ec21BankLoanBorrowedSpec extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    
    protected $fillable = [
        'bank_loan_borrowed_id',
        'bank_loan_scheme_specification_id',
        'bank_loan_schema_particular_id',  // Corrected from bank_loan_scheme_particular_id
        'bank_loan_schema_particular_value',  // Corrected from bank_loan_scheme_particular_value
        'is_percent_on_current_balance',
        'is_regular',
        'effected_on',
        'status',
        'user_id',
        'organisation_id',
        'financial_year_id',
        'is_active',
        'remarks'
    ];
    
    protected $casts = [
        'bank_loan_schema_particular_id' => 'integer',  // Corrected from bank_loan_scheme_particular_id
        'bank_loan_schema_particular_value' => 'decimal:2',  // Corrected from bank_loan_scheme_particular_value
        'is_percent_on_current_balance' => 'boolean',
        'is_regular' => 'boolean',
        'effected_on' => 'date',
        'is_active' => 'boolean',
    ];
    
    
    
    public function borrowedLoan()
    {
        return $this->belongsTo(Ec21BankLoanBorrowed::class, 'bank_loan_borrowed_id');
    }
    
    public function specification()
    {
        return $this->belongsTo(Ec21BankLoanSchemeSpecification::class, 'bank_loan_scheme_specification_id');
    }
    
    public function particular()
    {
        return $this->belongsTo(Ec21BankLoanSchemaParticular::class, 'bank_loan_schema_particular_id');  // Corrected from bank_loan_scheme_particular_id
    }
}