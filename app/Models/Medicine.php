<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'generic_name',
        'dosage_form',
        'strength',
        'unit',
        'quantity_on_hand',
        'reorder_level',
        'expiry_date',
        'remarks',
    ];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    public function dispenses()
    {
        return $this->hasMany(MedicineDispense::class);
    }
}
