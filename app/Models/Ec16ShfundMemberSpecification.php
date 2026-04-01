<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ec16ShfundMemberSpecification extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'ec16_shfund_member_specifications';
    
    public function memberMasterDb(): BelongsTo
    {
        return $this->belongsTo(Ec16ShfundMemberMasterDb::class, 'shfund_member_master_db_id');
    }
}
