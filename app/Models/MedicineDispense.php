<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineDispense extends Model
{
    use HasFactory;

    protected $fillable = [
        'medicine_id',
        'quantity',
        'dispensed_to',
        'reference_no',
        'dispensed_at',
        'remarks',
    ];

    protected $casts = [
        'dispensed_at' => 'date',
    ];

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }
}
