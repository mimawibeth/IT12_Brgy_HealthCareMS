<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrenatalRecord extends Model
{
    protected $fillable = [
        'record_no',
        'mother_name',
        'purok',
        'age',
        'dob',
        'occupation',
        'education',
        'is_4ps',
        'four_ps_no',
        'cell',
        'lmp',
        'edc',
        'urinalysis',
        'gravida',
        'para',
        'abortion',
        'delivery_count',
        'last_delivery_date',
        'delivery_type',
        'hemoglobin_first',
        'hemoglobin_second',
        'blood_type',
        'urinalysis_protein',
        'urinalysis_sugar',
        'husband_name',
        'husband_occupation',
        'husband_education',
        'family_religion',
        'amount_prepared',
        'philhealth_member',
        'delivery_location',
        'delivery_partner',
        'td1',
        'td2',
        'td3',
        'td4',
        'td5',
        'tdl',
        'fbs',
        'rbs',
        'ogtt',
        'vdrl',
        'hbsag',
        'hiv',
        'extra',
    ];

    protected $casts = [
        'dob' => 'date',
        'lmp' => 'date',
        'edc' => 'date',
        'last_delivery_date' => 'date',
        'td1' => 'date',
        'td2' => 'date',
        'td3' => 'date',
        'td4' => 'date',
        'td5' => 'date',
        'tdl' => 'date',
        'extra' => 'array',
    ];

    public function visits()
    {
        return $this->hasMany(PrenatalVisit::class);
    }
}
