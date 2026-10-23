<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $guarded = [];

    protected $keyType = 'string';

    public $incrementing = false;

    protected $casts = [
        'branch_id' => 'integer',
        'subtotal' => 'integer',
        'amount' => 'integer',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'integer',
        'discount_amount' => 'integer',
        'paid_at' => 'datetime',
        'due_at' => 'datetime',
    ];
}
