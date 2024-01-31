<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = "products";

    // protected $primaryKey = 'id_product';

    protected $guarded = [];

    protected $casts = [
        'qty' => 'integer',
        'price' => 'integer',
    ];

    public function order()
    {
        return $this->hasMany(OrderedItem::class);
    }
}
