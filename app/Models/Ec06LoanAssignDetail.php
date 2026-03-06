<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ec06LoanAssignDetail extends Model
{
    use HasFactory;
    protected $table = 'ec06_loan_assign_details';
    protected $guarded = ['id'];
    protected $fillable = [
        'loan_assign_id',
        'loan_scheme_detail_id',
        'loan_scheme_detail_feature_id',
        'loan_scheme_detail_feature_name',
        'loan_scheme_detail_feature_value',
        'loan_scheme_detail_feature_condition',
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

    public function loanAssign()
    {
        return $this->belongsTo(Ec05LoanAssign::class, 'loan_assign_id');
    }

    public function loanSchemeDetail()
    {
        return $this->belongsTo(Ec02LoanSchemeDetail::class, 'loan_scheme_detail_id');
    }
}
