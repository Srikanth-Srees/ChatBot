<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryAddress extends Model
{
    
    use HasFactory;

    protected $fillable = [
        'in_pin_code',
        'building_name',
        'landmark_area',
        'address',
        'tower_number',
        'city',
        'name',
        'phone_number',
        'house_number',
        'floor_number',
        'state',
        'transaction_id',
    ];

    // Define the relationship with the CustomerProductDetail model
    public function customerProductDetails()
    {
        return $this->hasMany(Customerproducts_Details::class, 'delivery_detail_id');
    }

    // Define the relationship with the Transaction model
    public function transaction()
    {
        return $this->belongsTo(transactions::class, 'transaction_id');
    }
}
