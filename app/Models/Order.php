<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice',
        'product_id',
        'qty',
        'total_price',
    ];

    protected $casts = [
        'product_id' => 'integer',
        'qty' => 'integer',
        'total_price' => 'integer',
    ];

    protected $with = ['product'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
