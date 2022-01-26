<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = "carts";

    protected $fillable = [
        'customer_id', 'device_id', 'product_qnty', 'total_purchase_price', 'total_sale_price',
        'total_discount', 'total_final_price', 'total_profit', 'cart_status', 'cart_by', 'notes'
    ];
}
