<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $guarded = ['id'];

    protected $fillable= [
        'image',
        'name',
        'slug',
        'price'
    ];
}
