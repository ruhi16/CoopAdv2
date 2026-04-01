<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ec17ShfundBankMasterDb extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'ec17_shfund_bank_master_dbs';
    
    protected static function boot()
    {
        parent::boot();
        
        static::deleting(function ($model) {
            $model->specifications()->delete();
            $model->transactions()->delete();
        });
    }
    
    public function bank(): BelongsTo
    {
        return $this->belongsTo(Ec20Bank::class, 'bank_id');
    }
    
    public function loanAssign(): BelongsTo
    {
        return $this->belongsTo(Ec05LoanAssign::class, 'loan_assign_id');
    }
    
    public function specifications(): HasMany
    {
        return $this->hasMany(Ec17ShfundBankSpecification::class, 'shfund_bank_master_db_id');
    }
    
    public function transactions(): HasMany
    {
        return $this->hasMany(Ec17ShfundBankTransaction::class, 'shfund_bank_master_db_id');
    }
}
