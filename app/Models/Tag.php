<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name', 'label', 'icon', 'color_class', 'theme'];

    protected $casts = [
        'theme' => 'array',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
