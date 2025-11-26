<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NipRecord extends Model
{
    protected $fillable = [
        'record_no',
        'date',
        'child_name',
        'dob',
        'address',
        'mother_name',
        'father_name',
        'contact',
        'place_delivery',
        'attended_by',
        'sex_baby',
        'nhts_4ps_id',
        'phic_id',
        'tt_status_mother',
        'birth_length',
        'birth_weight',
        'delivery_type',
        'initiated_breastfeeding',
        'birth_order',
        'newborn_screening_date',
        'newborn_screening_result',
        'hearing_test_screened',
        'vit_k',
        'bcg',
        'hepa_b_24h',
    ];

    protected $casts = [
        'date' => 'date',
        'dob' => 'date',
        'newborn_screening_date' => 'date',
    ];

    public function visits()
    {
        return $this->hasMany(NipVisit::class);
    }
}
