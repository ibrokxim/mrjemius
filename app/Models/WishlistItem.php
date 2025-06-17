<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WishlistItem extends Model
{
    use HasFactory;

    protected $table = 'wishlist_items';

    protected $fillable = [
        'user_id',
        'product_id',
    ];

    public function user(): belongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): belongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
