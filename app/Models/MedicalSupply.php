<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalSupply extends Model
{
    protected $fillable = [
        'item_name',
        'category',
        'description',
        'unit_of_measure',
        'quantity_on_hand',
    ];

    public function supplyHistory()
    {
        return $this->hasMany(SupplyHistory::class);
    }
}
