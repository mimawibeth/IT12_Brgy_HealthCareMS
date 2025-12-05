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
        'reorder_level',
        'remarks',
    ];

    public function dispenses()
    {
        return $this->hasMany(MedicineDispense::class);
    }

    public function batches()
    {
        return $this->hasMany(MedicineBatch::class);
    }

    public function getQuantityOnHandAttribute()
    {
        if ($this->relationLoaded('batches')) {
            return (int) $this->batches->sum('quantity_on_hand');
        }

        return (int) $this->batches()->sum('quantity_on_hand');
    }

    public function getExpiryDateAttribute()
    {
        if ($this->relationLoaded('batches')) {
            $batch = $this->batches
                ->where('quantity_on_hand', '>', 0)
                ->sortBy('expiry_date')
                ->first();
        } else {
            $batch = $this->batches()
                ->where('quantity_on_hand', '>', 0)
                ->orderBy('expiry_date')
                ->first();
        }

        return optional($batch)->expiry_date;
    }
}
