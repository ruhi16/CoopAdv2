<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ec02LoanSchemeDetail extends Model
{
    use HasFactory;
    protected $table = 'ec02_loan_scheme_details';
    protected $guarded = ['id'];
    protected $fillable = [
        'loan_scheme_id',
        'loan_scheme_feature_id',
        'loan_scheme_feature_name',
        'loan_scheme_feature_type',
        'loan_scheme_feature_value_type',
        'loan_scheme_feature_value',
        'loan_scheme_feature_condition',
        'name',
        'description',
        'order_index',
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

    public function loanSchemeFeature()
    {
        return $this->belongsTo(Ec02LoanSchemeFeature::class, 'loan_scheme_feature_id');
    }
}
