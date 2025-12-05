<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MedicineDispense;

class MedicineBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'medicine_id',
        'batch_code',
        'quantity_on_hand',
        'expiry_date',
        'date_received',
        'supplier',
        'unit_price',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'date_received' => 'date',
        'unit_price' => 'decimal:2',
    ];

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    public function dispenses()
    {
        return $this->belongsToMany(MedicineDispense::class, 'medicine_dispense_batches')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
