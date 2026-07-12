<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingOptionModel extends Model
{
    /**
     * Dummy model to prevent Eloquent instantiation crashes when serializing and
     * deserializing polymorphic relationships involving Lunar's non-model ShippingOption class.
     */
    protected $table = 'lunar_orders';
}
