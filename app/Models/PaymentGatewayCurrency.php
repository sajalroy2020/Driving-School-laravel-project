<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentGatewayCurrency extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['gateway_id', 'currency', 'conversion_rate'];
}