<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ec17ShfundBankMasterDb extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    
    public function bank(): BelongsTo
    {
        return $this->belongsTo(Ec20Bank::class, 'bank_id', 'id');
    }
    
    public function loanAssign(): BelongsTo
    {
        return $this->belongsTo(Ec08LoanAssign::class, 'loan_assign_id', 'id');
    }
}
