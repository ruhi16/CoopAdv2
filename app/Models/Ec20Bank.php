<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ec20Bank extends Model
{
    use HasFactory;
    protected $guarded = ['id'];



    public function details()
    {
        return $this->hasMany(Ec20BankDetail::class, 'bank_id');
    }
}
