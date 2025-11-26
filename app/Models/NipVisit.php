<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NipVisit extends Model
{
    protected $fillable = [
        'nip_record_id',
        'age_months',
        'weight',
        'length',
        'breastfeeding',
        'temperature',
        'vaccine',
    ];

    public function record()
    {
        return $this->belongsTo(NipRecord::class, 'nip_record_id');
    }
}
