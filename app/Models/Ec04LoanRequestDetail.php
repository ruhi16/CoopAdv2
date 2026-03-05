<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ec04LoanRequestDetail extends Model
{
    use HasFactory;
    protected $table = 'ec04_loan_request_details';
    protected $guarded = ['id'];
    protected $fillable = [
        'loan_request_id',
        'loan_scheme_detail_id',
        'loan_scheme_feature_id',
        'loan_scheme_feature_name',
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

    public function loanRequest()
    {
        return $this->belongsTo(Ec03LoanRequest::class, 'loan_request_id');
    }

    public function loanSchemeDetail()
    {
        return $this->belongsTo(Ec02LoanSchemeDetail::class, 'loan_scheme_detail_id');
    }

    public function loanSchemeFeature()
    {
        return $this->belongsTo(Ec02LoanSchemeFeature::class, 'loan_scheme_feature_id');
    }
}
