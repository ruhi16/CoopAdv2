<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ec01LoanScheme extends Model
{
    use HasFactory;
    protected $table = 'ec01_loan_schemes';
    protected $guarded = ['id'];
    protected $fillable = [
        'name',
        'description',
        'order_index',
        'with_effect_from',
        'with_effect_to',
        'is_default',
        'is_active',
        'created_by',
        'approved_by',
        'school_id',
        'remarks',
        'status',
    ];

    public function loanSchemeFeatures()
    {
        return $this->hasMany(Ec02LoanSchemeDetail::class, 'loan_scheme_id', 'id');
    }


    public function loanScheme()
    {
        return $this->hasMany(Ec03LoanRequest::class, 'loan_scheme_id', 'id');
    }




}
