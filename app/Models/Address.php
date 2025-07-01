<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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

    public function user(): belongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function full_address(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                // Собираем массив из частей адреса, которые не пустые
                $addressParts = array_filter([
                    $attributes['postal_code'] ?? null,
                    $attributes['city'] ?? null,
                    $attributes['address_line_1'] ?? null,
                    $attributes['address_line_2'] ?? null,
                ]);

                // Объединяем части в одну строку через запятую и пробел
                return implode(', ', $addressParts);
            }
        );
    }
}
