<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ec05LoanAssign extends Model
{
    use HasFactory;
    protected $table = 'ec05_loan_assigns';
    protected $guarded = ['id'];
    protected $fillable = [
        'member_id',
        'loan_request_id',
        'loan_scheme_id',
        'name',
        'description',
        'order_index',
        'loan_assigned_date',
        'loan_released_date',
        'loan_closed_date',
        'loan_amount',
        'loan_current_balance',
        'roi',
        'is_emi_enabled',
        'no_of_emi',
        'emi_amount',
        'first_emi_due_date',
        'next_emi_due_date',
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
        return $this->belongsTo(Ec01LoanScheme::class, 'loan_scheme_id');
    }

    public function loanRequest()
    {
        return $this->belongsTo(Ec03LoanRequest::class, 'loan_request_id');
    }

    public function loanAssignDetails()
    {
        return $this->hasMany(Ec06LoanAssignDetail::class, 'loan_assign_id');
    }

    public function emiSchedules()
    {
        return $this->hasMany(Ec07LoanEmiSchedule::class, 'loan_assign_id');
    }
}
