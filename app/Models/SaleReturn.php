<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'sale_item_id',
        'medicine_id',
        'returned_quantity',
        'return_amount'
    ];

    public function saleItem()
    {
        return $this->belongsTo(SaleItem::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }
}
