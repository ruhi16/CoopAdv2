<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ec16ShfundMemberMasterDb extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    
    public function member(): BelongsTo
    {
        return $this->belongsTo(Ec04Member::class, 'member_id', 'id');
    }
    
    public function loanAssign(): BelongsTo
    {
        return $this->belongsTo(Ec08LoanAssign::class, 'loan_assign_id', 'id');
    }
}
