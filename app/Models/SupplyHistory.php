<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplyHistory extends Model
{
    protected $table = 'supply_history';

    protected $fillable = [
        'medical_supply_id',
        'item_name',
        'quantity',
        'received_from',
        'date_received',
        'handled_by',
    ];

    protected $casts = [
        'date_received' => 'date',
    ];

    public function medicalSupply()
    {
        return $this->belongsTo(MedicalSupply::class);
    }
}
