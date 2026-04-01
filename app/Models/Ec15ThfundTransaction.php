<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ec15ThfundTransaction extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'ec15_thfund_transactions';
    
    protected $casts = [
        'transaction_date' => 'datetime',
    ];
    
    public function masterDb(): BelongsTo
    {
        return $this->belongsTo(Ec15ThfundMasterDb::class, 'thfund_master_db_id');
    }
    
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id');
    }
}
