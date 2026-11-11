<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';

    protected $fillable = [
        'transaction_number',
        'user_id',
        'customer_id',
        'subtotal',
        'discount',
        'tax',
        'other_fee',
        'grand_total',
        'paid_amount',
        'change_amount',
        'payment_method',
        'status',
        'midtrans_order_id',
        'snap_token',
        'paid_at',
        'stock_deducted_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'other_fee' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'stock_deducted_at' => 'datetime',
    ];
}