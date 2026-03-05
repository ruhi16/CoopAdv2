<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberDb extends Model{
    use HasFactory;
    protected $table = 'member_dbs';

    protected $guarded = ['id'];

    protected $fillable = [
        'member_type_id',
        'name',
        'name_short',
        'father_name',
        'school_designation',
        'email',
        'phone',
        'mobile',
        'address',
        'dob',
        'doj',
        'dor',
        'gender',
        'nationality',
        'religion',
        'marital_status',
        'blood_group',
        'pan_no',
        'aadhar_no',
        'voter_id_no',
        'account_bank',
        'account_branch',
        'account_no',
        'account_ifsc',
        'account_micr',
        'account_customer_id',
        'account_holder_name',
        'is_default',
        'is_active',
        'created_by',
        'approved_by',
        'school_id',
        'remarks',
        'status',
    ];


    protected static function booted(){
        static::addGlobalScope('active', function ($builder) {
            $builder->where('is_active', true);
        });
    }

    public function memberType()
    {
        return $this->belongsTo(MemberType::class, 'member_type_id');
    }

    public function members()
    {
        return $this->hasMany(Member::class, 'member_db_id');
    }

    public static function getAvailableFinancialYears()
    {
        return static::withoutGlobalScopes()
            ->select('doj')
            ->whereNotNull('doj')
            ->distinct()
            ->orderBy('doj', 'desc')
            ->get()
            ->map(function ($item) {
                $year = date('Y', strtotime($item->doj));
                $month = date('m', strtotime($item->doj));
                if ($month >= 4) {
                    return $year . '-' . ($year + 1);
                } else {
                    return ($year - 1) . '-' . $year;
                }
            })
            ->unique()
            ->values();
    }
}
