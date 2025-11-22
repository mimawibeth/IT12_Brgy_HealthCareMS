<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $primaryKey = 'PatientID';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'dateRegistered',
        'patientNo',
        'sex',
        'name',
        'birthday',
        'contactNumber',
        'address',
        'nhtsIdNo',
        'pwdIdNo',
        'phicIdNo',
        'fourPsCctIdNo',
        'ethnicGroup',
        'diabetesDate',
        'hypertensionDate',
        'copdDate',
        'asthmaDate',
        'cataractDate',
        'eorDate',
        'diabeticRetinopathyDate',
        'otherEyeDiseaseDate',
        'alcoholismDate',
        'substanceAbuseDate',
        'otherMentalDisordersDate',
        'atRiskSuicideDate',
        'philpenDate',
        'currentSmoker',
        'passiveSmoker',
        'stoppedSmoking',
        'drinksAlcohol',
        'hadFiveDrinks',
        'dietaryRiskFactors',
        'physicalInactivity',
        'height',
        'weight',
        'waistCircumference',
        'bmi',
        'whoDasDate',
        'part1',
        'part2Score',
        'top1Domain',
        'top2Domain',
        'top3Domain',
        'lengthDiabetes',
        'lengthHypertension',
        'floaters',
        'blurredVision',
        'fluctuatingVision',
        'impairedColorVision',
        'darkEmptyAreas',
        'visionLoss',
        'visualAcuityLeft',
        'visualAcuityRight',
        'ophthalmoscopyResults',
    ];

    public function assessments()
    {
        return $this->hasMany(Assessment::class, 'PatientID', 'PatientID');
    }
}
