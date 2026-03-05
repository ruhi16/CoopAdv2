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
        'join_date',
        'exit_date',
        'loan_amount',
        'loan_current_balance',
        'roi',
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
}
