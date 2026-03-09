<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ec21BankLoanSchemaParticular extends Model
{
    use HasFactory;
    protected $table = 'ec21_bank_loan_schema_particulars';

    protected $fillable = [
        'name',
        'description',
        'is_optional',
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
        'is_optional' => 'boolean',
        'is_finalized' => 'boolean',
        'finalized_at' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get all specifications that use this particular
     */
    public function specifications(): HasMany
    {
        return $this->hasMany(Ec21BankLoanSchemeSpecification::class, 'bank_loan_schema_particular_id');
    }

    /**
     * Get the user who created the particular
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the organization that owns the particular
     */
    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Ec01Organisation::class, 'organisation_id');
    }

    /**
     * Get the financial year for the particular
     */
    public function financialYear(): BelongsTo
    {
        return $this->belongsTo(Ec02FinancialYear::class, 'financial_year_id');
    }
}
