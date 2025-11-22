<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'PatientID',
        'date',
        'age',
        'cvdRisk',
        'bpSystolic',
        'bpDiastolic',
        'wt',
        'ht',
        'fbsRbs',
        'lipidProfile',
        'urineKetones',
        'urineProtein',
        'footCheck',
        'chiefComplaint',
        'historyPhysical',
        'management',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'PatientID', 'PatientID');
    }
}
