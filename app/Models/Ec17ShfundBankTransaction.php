<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ec17ShfundBankTransaction extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    
    public function member(): BelongsTo
    {
        return $this->belongsTo(Ec04Member::class, 'member_id', 'id');
    }
}
