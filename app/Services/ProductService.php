<?php

namespace App\Services;

use App\Models\Product;
use App\Traits\HasUniqueSlug;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductService
{
    use HasUniqueSlug;

    protected ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }
    public function getBestSellerProducts(): ?array
    {
        $products =  $this->productRepository->getBestSellerProducts();
        if ($products->isEmpty()) {
            throw new NotFoundHttpException('Products not found');
        }
        return $products;
    }

    public function getProductBySlug(string $slug): ?Product
    {
        $product = $this->productRepository->getProductBySlug($slug);
        if (!$product) {
            throw new NotFoundHttpException('Product not found');
        }
        return $product;
    }

    public function getAllProducts()
    {
        return $this->productRepository->getAllProducts();
    }

    public function createProduct(array $data)
    {
        return DB::transaction(function () use ($data) {
            $productData = $this->prepareProductCoreData($data);
            $product = $this->productRepository->createProduct($productData);

            $this->processAndSaveRelatedData($product, $productData);

            return $this->productRepository->getProductBySlug($product->slug, ['category', 'ceo', 'tags', 'images']);
        });
    }

    private function prepareProductCoreData(Product $product): array
    {
        $coreData = [
            'category_id'       => $inputData['category_id'] ?? null,
            'name'              => $inputData['name'],
            'sku'               => $inputData['sku'] ?? null,
            'description'       => $inputData['description'] ?? null,
            'short_description' => $inputData['short_description'] ?? null,
            'price'             => $inputData['price'],
            'sale_price'        => $inputData['sale_price'] ?? null,
            'stock_quantity'    => $inputData['stock_quantity'],
            'is_active'         => $inputData['is_active'] ?? true,
            'is_featured'       => $inputData['is_featured'] ?? false,
            'weight_kg'         => $inputData['weight_kg'] ?? null,
            'attributes'        => $inputData['attributes'] ?? null,
        ];

        // Генерация slug
        if (empty($inputData['slug']) && !empty($inputData['name'])) {
            $coreData['slug'] = $this->generateUniqueSlugUsingCallback(
                $inputData['name'],
                [$this->productRepository, 'slugExists']
            );
        } elseif (!empty($inputData['slug'])) {
            $coreData['slug'] = $this->generateUniqueSlugUsingCallback(
                $inputData['slug'],
                [$this->productRepository, 'slugExists']
            );
        } else {
            $coreData['slug'] = null;
        }

        return $coreData;
    }

    private function processAndSaveRelatedData(Product $product, array $data)
    {

    }
}
