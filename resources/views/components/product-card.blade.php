@props(['product'])

@php
    // Вычисляем процент скидки, если есть акционная цена
    $discountPercentage = 0;
    if ($product->sell_price && $product->price > 0 && $product->sell_price < $product->price) { // Добавил проверку sell_price < price
        $discountPercentage = round((($product->price - $product->sell_price) / $product->price) * 100);
    }

    // Проверяем, находится ли товар в избранном у текущего пользователя
    $isInWishlist = false;
    if (Auth::check()) { // Проверяем, что пользователь аутентифицирован
        $isInWishlist = Auth::user()->wishlistProducts()->where('product_id', $product->id)->exists();
    }
@endphp

<div class="card card-product h-100">
    <div class="card-body d-flex flex-column">
        <div class="text-center position-relative">
            {{-- Отображение бейджа со скидкой --}}
            @if($discountPercentage > 0)
                <div class="position-absolute top-0 start-0">
                    <span class="badge" style="background-color: #f5c518; color: #000;">-{{ $discountPercentage }}%</span>
                </div>
            @endif

            {{-- Изображение и ссылка на страницу продукта --}}
            <a href="{{ route('product.show', $product->slug) }}">
                <img src="{{ $product->primaryImage ? Storage::disk('public')->url($product->primaryImage->image_url) : asset('assets/images/placeholder.png') }}"
                     alt="{{ $product->name }}" class="mb-3 img-fluid" style="height: 150px; object-fit: contain;"
                     onerror="this.onerror=null;this.src='{{ asset('assets/images/placeholder.png') }}';">
            </a>

            {{-- Кнопки действий при наведении --}}
            <div class="card-product-action">
                <a href="#!" class="btn-action" data-bs-toggle="modal" data-bs-target="#quickViewModal" data-product-id="{{ $product->id }}">
                    <i class="bi bi-eye" data-bs-toggle="tooltip" data-bs-html="true" title="Быстрый просмотр"></i>
                </a>
                {{-- Кнопка "В избранное" --}}
                @auth {{-- Показываем кнопку только аутентифицированным пользователям --}}
                <button class="btn-action wishlist-toggle-btn {{ $isInWishlist ? 'active text-danger' : '' }}"
                        data-product-id="{{ $product->id }}"
                        data-bs-toggle="tooltip" data-bs-html="true"
                        title="{{ $isInWishlist ? 'Убрать из избранного' : 'В избранное' }}">
                    <i class="bi {{ $isInWishlist ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                </button>
                @else
                    <a href="#" class="btn-action" data-bs-toggle="modal" data-bs-target="#userModal" title="Войдите, чтобы добавить в избранное">
                        <i class="bi bi-heart"></i>
                    </a>

                @endauth
            </div>
        </div>

        {{-- Контейнер для цены и названия, чтобы он был прижат к низу --}}
        <div class="mt-auto">
            {{-- Блок с ценой --}}
            <div class="mt-3">
                @if($product->sell_price && $product->sell_price < $product->price)
                    <span class="fs-5 fw-bold text-dark">{{ number_format($product->sell_price) }} сум</span>
                    <span class="text-decoration-line-through text-muted ms-1">{{ number_format($product->price) }} сум</span>
                @else
                    <span class="fs-5 fw-bold text-dark">{{ number_format($product->price) }} сум.</span>
                @endif
            </div>

            {{-- Название товара --}}
            <h2 class="fs-6 mt-2"><a href="{{ route('product.show', $product->slug) }}" class="text-inherit text-decoration-none">{{ $product->name }}</a></h2>

            {{-- Категория (опционально) --}}
            <div class="text-small text-muted">
                {{ $product->category->name ?? 'Без категории' }}
            </div>

            {{-- Кнопка "В корзину" --}}
            <div class="d-grid mt-2">
                {{-- Здесь будет ваша логика для кнопки "В корзину" --}}
                <button class="btn btn-primary add-to-cart-btn" data-product-id="{{ $product->id }}">
                    В корзину
                </button>
            </div>
        </div>
    </div>
</div>
