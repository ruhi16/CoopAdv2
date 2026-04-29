<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ec03LoanRequest extends Model
{
    use HasFactory;
    protected $table = 'ec03_loan_requests';
    protected $guarded = ['id'];
    protected $fillable = [
        'member_id',
        'loan_scheme_id',
        'loan_amount',
        'no_of_years',
        'emi_active',
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

    public function member()
    {
        return $this->belongsTo(MemberDb::class, 'member_id');
    }

    public function loanScheme()
    {
        return $this->belongsTo(Ec01LoanScheme::class, 'loan_scheme_id', 'id');
    }

    public function loanRequestDetails()
    {
        return $this->hasMany(Ec04LoanRequestDetail::class, 'loan_request_id', 'id');
    }

    public function emiSchedules()
    {
        return $this->hasMany(Ec07LoanEmiSchedule::class, 'loan_request_id');
    }

    public function loanAssigns()
    {
        return $this->hasMany(Ec05LoanAssign::class, 'loan_request_id');
    }
}
