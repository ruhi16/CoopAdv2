<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;
    protected $table = 'schools';
    protected $guarded = ['id'];

    protected $fillable = [
        'name',
        'code',
        'address',
        'phone',
        'email',
    ];

    public function users(){
        return $this->hasMany(User::class, 'school_id', 'id');
    }

    public function members(){
        return $this->hasMany(Member::class, 'school_id', 'id');
    }

    
}
