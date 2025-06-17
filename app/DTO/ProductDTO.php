<?php

namespace App\DTO;

use App\Models\Product;

class ProductDTO
{

    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly string $shortDescription,
        public readonly float $price,
        public readonly ?float $salePrice,
        public readonly bool $onSale,
        public readonly ?string $primaryImageUrl,
        public readonly ?string $categoryName,
    )
    {
    }

    public static function fromModel(Product $product): self
    {
        return new self(
            id: $product->id,
            name: $product->name,
            slug: $product->slug,
            shortDescription: $product->short_description,
            price: (float) $product->price,
            salePrice: $product->sale_price ? (float) $product->sale_price : null,
            onSale: !is_null($product->sale_price) && $product->sale_price < $product->price,
            primaryImageUrl: $product->primaryImage ? asset($product->primaryImage->image_url) : null,
            categoryName: $product->category?->name
        );
    }

}
