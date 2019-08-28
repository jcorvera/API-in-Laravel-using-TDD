<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $table = 'images';

    protected $guard = ['id'];

    protected $fillable= [
        'path',
    ];

    public function product()
    {
        return $this->belongsTo('App\Models\Product\Product');
    }
}
