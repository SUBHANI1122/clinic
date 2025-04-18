<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medicine extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'added_by',
        'name',
        'size',
        'box_quantity',
        'units_per_box',
        'price',
        'sale_price',
        'price_per_unit',
        'sale_price_per_unit',
        'total_units'
    ];
}