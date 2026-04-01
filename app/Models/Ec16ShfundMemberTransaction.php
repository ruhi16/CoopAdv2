<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ec16ShfundMemberTransaction extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'ec16_shfund_member_transactions';
    
    protected $casts = [
        'transaction_date' => 'datetime',
    ];
    
    public function memberMasterDb(): BelongsTo
    {
        return $this->belongsTo(Ec16ShfundMemberMasterDb::class, 'shfund_member_master_db_id');
    }
    
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id');
    }
    
    public function loanAssign(): BelongsTo
    {
        return $this->belongsTo(Ec05LoanAssign::class, 'loan_assign_id');
    }
}
