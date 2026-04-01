<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ec17ShfundBankSpecification extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'ec17_shfund_bank_specifications';
    
    public function bankMasterDb(): BelongsTo
    {
        return $this->belongsTo(Ec17ShfundBankMasterDb::class, 'shfund_bank_master_db_id');
    }
}
