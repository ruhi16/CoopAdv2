<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ec15ThfundMasterDb extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'ec15_thfund_master_dbs';
    
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
    
    public function specifications(): HasMany
    {
        return $this->hasMany(Ec15ThfundSpecification::class, 'thfund_master_db_id');
    }
    
    public function transactions(): HasMany
    {
        return $this->hasMany(Ec15ThfundTransaction::class, 'thfund_master_db_id');
    }
}
