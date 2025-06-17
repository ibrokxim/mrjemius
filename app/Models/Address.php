<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    use HasFactory;

    protected $table = 'addresses';

    protected $fillable = ['user_id',
        'type',
        'full_name',
        'address_line_1',
        'address_line_2',
        'city',
        'state_province',
        'postal_code',
        'country_code',
        'phone_number',
        'is_default', ];

    protected $casts = ['is_default' => 'boolean'];

    // Пользователь которому принадлежит адрес.
    public function user(): belongsTo
    {
        return $this->belongsTo(User::class);
    }
}
