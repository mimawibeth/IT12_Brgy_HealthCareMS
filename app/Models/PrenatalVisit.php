<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrenatalVisit extends Model
{
    protected $fillable = [
        'prenatal_record_id',
        'date',
        'trimester',
        'risk',
        'first_visit',
        'subjective',
        'aog',
        'weight',
        'height',
        'bp',
        'pr',
        'fh',
        'fht',
        'presentation',
        'bmi',
        'rr',
        'hr',
        'assessment',
        'plan',
    ];

    public function record()
    {
        return $this->belongsTo(PrenatalRecord::class, 'prenatal_record_id');
    }
}
