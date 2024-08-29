<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customerproducts_Details extends Model
{
    use HasFactory;

    protected $table = 'customerproducts__details';

    protected $fillable = [
        'name',
        'mobile',
        'amount',
        'ordered_products',
        'delivery_detail_id',
        'delivery_detail_status',
        'transaction_detail_id',
        'transaction_detail_status',
    ];

    // Define the relationship with the DeliveryAddress model
    public function deliveryDetail()
    {
        return $this->belongsTo(DeliveryAddress::class, 'delivery_detail_id');
    }

    // Define the relationship with the Transaction model
    public function transactionDetail()
    {
        return $this->belongsTo(transactions::class, 'transaction_detail_id');
    }

}
