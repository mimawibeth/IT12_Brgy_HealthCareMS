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
    ];

    protected $casts = [
        'date' => 'date',
        'dob' => 'date',
    ];

    public function visits()
    {
        return $this->hasMany(NipVisit::class);
    }
}
