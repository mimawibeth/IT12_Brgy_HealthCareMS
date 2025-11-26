<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FamilyPlanningRecord extends Model
{
    protected $fillable = [
        'record_no',
        'client_name',
        'dob',
        'age',
        'address',
        'contact',
        'occupation',
        'spouse_name',
        'spouse_age',
        'children_count',
        'client_type',
        'reason',
        'medical_history',
        'gravida',
        'para',
        'last_delivery',
        'last_period',
        'menstrual_flow',
        'dysmenorrhea',
        'sti_risk',
        'vaw_risk',
        'bp',
        'weight',
        'height',
        'exam_findings',
        'counseled_by',
        'client_signature',
        'consent_date',
    ];

    protected $casts = [
        'dob' => 'date',
        'last_delivery' => 'date',
        'last_period' => 'date',
        'consent_date' => 'date',
        'reason' => 'array',
        'medical_history' => 'array',
        'sti_risk' => 'array',
        'vaw_risk' => 'array',
    ];
}
