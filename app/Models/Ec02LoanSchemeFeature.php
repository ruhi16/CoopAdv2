<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ec02LoanSchemeFeature extends Model
{
    use HasFactory;
    protected $table = 'ec02_loan_scheme_features';
    protected $guarded = ['id'];
    protected $fillable = [
        'loan_scheme_id',
        'name',
        'description',
        'order_index',
        'feature_type',
        'feature_value_type',
        'is_default',
        'is_active',
        'created_by',
        'approved_by',
        'school_id',
        'remarks',
        'status',
    ];

    public function loanScheme()
    {
        return $this->belongsTo(Ec01LoanScheme::class, 'loan_scheme_id');
    }
}
