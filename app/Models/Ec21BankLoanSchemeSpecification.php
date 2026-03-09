<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ec21BankLoanSchemeSpecification extends Model
{
    use HasFactory;
    protected $table = 'ec21_bank_loan_scheme_specifications';

    protected $fillable = [
        'name',
        'description',
        'bank_loan_scheme_id',
        'bank_loan_schema_particular_id',
        'bank_loan_schema_particular_value',
        'is_percent_on_current_balance',
        'is_regular',
        'effected_on',
        'task_execution_id',
        'status',
        'user_id',
        'organisation_id',
        'financial_year_id',
        'is_finalized',
        'finalized_by',
        'finalized_at',
        'is_active',
        'remarks'
    ];

    protected $casts = [
        'bank_loan_schema_particular_value' => 'decimal:2',
        'is_percent_on_current_balance' => 'boolean',
        'is_regular' => 'boolean',
        'effected_on' => 'date',
        'finalized_at' => 'date',
        'is_finalized' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the loan scheme that owns the specification
     */
    public function loanScheme(): BelongsTo
    {
        return $this->belongsTo(Ec21BankLoanScheme::class, 'bank_loan_scheme_id');
    }

    /**
     * Get the particular that owns the specification
     */
    public function particular(): BelongsTo
    {
        return $this->belongsTo(Ec21BankLoanSchemaParticular::class, 'bank_loan_schema_particular_id');
    }

    /**
     * Get the user who created the specification
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the organization that owns the specification
     */
    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Ec01Organisation::class, 'organisation_id');
    }

    /**
     * Get the financial year for the specification
     */
    public function financialYear(): BelongsTo
    {
        return $this->belongsTo(Ec02FinancialYear::class, 'financial_year_id');
    }
}
