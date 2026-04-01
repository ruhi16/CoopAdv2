<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ec15ThfundSpecification extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'ec15_thfund_specifications';
    
    public function masterDb(): BelongsTo
    {
        return $this->belongsTo(Ec15ThfundMasterDb::class, 'thfund_master_db_id');
    }
}
