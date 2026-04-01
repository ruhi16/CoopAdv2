<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ec17ShfundBankTransaction extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'ec17_shfund_bank_transactions';
    
    protected $casts = [
        'transaction_date' => 'datetime',
    ];
    
    public function bankMasterDb(): BelongsTo
    {
        return $this->belongsTo(Ec17ShfundBankMasterDb::class, 'shfund_bank_master_db_id');
    }
    
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id');
    }
}
