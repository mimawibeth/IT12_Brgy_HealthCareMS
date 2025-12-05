<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MedicineBatch;

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

    public function batches()
    {
        return $this->belongsToMany(MedicineBatch::class, 'medicine_dispense_batches')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
