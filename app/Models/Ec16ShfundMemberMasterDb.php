<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ec16ShfundMemberMasterDb extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'ec16_shfund_member_master_dbs';
    
    protected static function boot()
    {
        parent::boot();
        
        static::deleting(function ($model) {
            $model->specifications()->delete();
            $model->transactions()->delete();
        });
    }
    
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id');
    }
    
    public function loanAssign(): BelongsTo
    {
        return $this->belongsTo(Ec05LoanAssign::class, 'loan_assign_id');
    }
    
    public function specifications(): HasMany
    {
        return $this->hasMany(Ec16ShfundMemberSpecification::class, 'shfund_member_master_db_id');
    }
    
    public function transactions(): HasMany
    {
        return $this->hasMany(Ec16ShfundMemberTransaction::class, 'shfund_member_master_db_id');
    }
}
