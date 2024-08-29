<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Customerproducts_Details;

class transactions extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'transactionId',
        'amount',
        'status',
        'type',
        'card_type',
        'bank_transaction_id',
        'bank_id',
        'complete_details',
    ];

    // Define the relationship with the CustomerProductDetail model
    public function customerProductDetails()
    {
        return $this->hasMany(Customerproducts_Details::class, 'transaction_detail_id');
    }

    // Define the relationship with the DeliveryAddress model
    public function deliveryAddresses()
    {
        return $this->hasMany(DeliveryAddress::class, 'transaction_id');
    }
}
