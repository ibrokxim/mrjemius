<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    use HasFactory;

    protected $table = 'product_images';

    protected $fillable = ['product_id',
        'image_url',
        'alt_text',
        'is_primary',
        'sort_order', ];

    protected $casts = ['is_primary' => 'boolean'];

    public function product(): belongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
