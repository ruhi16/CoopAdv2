<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ec15ThfundMasterDb extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function member(){
        return $this->belongsTo(Ec04Member::class, 'member_id', 'id');
    }


}
