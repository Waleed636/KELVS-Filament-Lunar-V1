<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartialOrder extends Model
{
    protected $fillable = [
        'session_id',
        'name',
        'phone',
        'email',
        'address',
        'city',
        'province',
        'postalcode',
        'cart_contents',
        'cart_total',
        'recovery_email_count',
        'last_recovery_email_sent_at',
    ];

    protected $casts = [
        'cart_contents' => 'array',
        'cart_total' => 'decimal:2',
        'recovery_email_count' => 'integer',
        'last_recovery_email_sent_at' => 'datetime',
    ];
}
