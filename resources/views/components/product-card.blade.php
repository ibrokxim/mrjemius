@props(['product'])

@php
    // Вычисляем процент скидки, если есть акционная цена
    $discountPercentage = 0;
    if ($product->sell_price && $product->price > 0 && $product->sell_price < $product->price) {
        $discountPercentage = round((($product->price - $product->sell_price) / $product->price) * 100);
    }

    $isInWishlist = false;
    if (Auth::check()) {
        $isInWishlist = Auth::user()->wishlistProducts()->where('product_id', $product->id)->exists();
    }
@endphp

<div class="card card-product h-100 clickable-card" data-url="{{ route('product.show', $product->slug) }}" style="cursor: pointer;">
    <div class="card-body d-flex flex-column">
        <div class="position-relative text-center mb-3">
            <div class="d-flex justify-content-between align-items-center position-absolute top-0 start-0 w-100"
                 style="padding-left: 0.5rem; padding-right: 0.25rem; top: -5px !important; z-index: 10;">
                <div>
                    @if($discountPercentage > 0)
                        <span class="badge stop-propagation" style="background-color: #f5c518; color: #000; padding: 0.35em 0.65em; font-size: 0.75em;">-{{ $discountPercentage }}%</span>
                    @else
                        <span class="stop-propagation"> </span>
                    @endif
                </div>
                <div style="margin-right: -0.25rem;">
                    @auth
                        <button class="btn-action wishlist-toggle-btn stop-propagation {{ $isInWishlist ? 'active text-danger' : '' }}"
                                style="background-color: white; border: 1px solid #dee2e6; border-radius: 0.375rem; padding: 0.25rem 0.5rem; font-size: 1rem; line-height: 1; box-shadow: 0 .125rem .25rem rgba(0,0,0,.075);"
                                data-product-id="{{ $product->id }}"
                                data-bs-toggle="tooltip" data-bs-html="true"
                                title="{{ $isInWishlist ? 'Убрать из избранного' : 'В избранное' }}">
                            <i class="bi {{ $isInWishlist ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                        </button>
                    @else
                        <a href="#" class="btn-action stop-propagation"
                           style="background-color: white; border: 1px solid #dee2e6; border-radius: 0.375rem; padding: 0.25rem 0.5rem; font-size: 1rem; line-height: 1; box-shadow: 0 .125rem .25rem rgba(0,0,0,.075); color: #495057;"
                           data-bs-toggle="modal" data-bs-target="#userModal" title="Войдите, чтобы добавить в избранное">
                            <i class="bi bi-heart"></i>
                        </a>
                    @endauth
                </div>
            </div>

            <img
                src="{{ $product->primaryImage ? Storage::disk('public')->url($product->primaryImage->image_url) : asset('assets/images/placeholder.png') }}"
                alt="{{ $product->name }}"
                class="img-fluid"
                style="height: 150px; object-fit: contain; margin-top: 1.5rem;"
                onerror="this.onerror=null;this.src='{{ asset('assets/images/placeholder.png') }}';">
        </div>

        <div class="mt-auto">
            <h2 class="fs-6 mt-2">{{ $product->name }}</h2>

            <div class="mt-3 price-block">
                @if($product->sell_price && $product->sell_price < $product->price)
                    {{-- Сначала обычная цена, перечеркнутая и меньше размером --}}
                    <div class="original-price text-muted text-decoration-line-through" style="font-size: 0.85em; line-height: 1;">
                        {{ number_format($product->price,  0, '', ' ') }} сум
                    </div>
                    {{-- Потом скидочная цена, крупнее и жирнее --}}
                    <div class="sale-price fs-5 fw-bold text-dark" style="line-height: 1.2;">
                        {{ number_format($product->sell_price,  0, '', ' ') }} сум
                    </div>
                @else
                    {{-- Если скидки нет, просто обычная цена --}}
                    <div class="regular-price fs-5 fw-bold text-dark">
                        {{ number_format($product->price, 0, '', ' ') }} сум
                    </div>
                @endif
            </div>

            <div class="text-small text-muted">
                {{ $product->category->name ?? 'Без категории' }}
            </div>

            <div class="d-grid mt-2">
                @auth
                    @if($product->stock_quantity > 0)
                        @php
                            $productInCart = Auth::user()->cartItems()->where('product_id', $product->id)->exists();
                        @endphp
                        <button class="btn {{ $productInCart ? 'btn-success' : 'btn-primary' }} add-to-cart-btn d-flex align-items-center justify-content-center text-nowrap"
                                data-product-id="{{ $product->id }}"
                                data-product-name="{{ $product->name }}"
                                data-product-price="{{ $product->sell_price ?? $product->price }}"
                                data-stock-quantity="{{ $product->stock_quantity }}"
                            {{ $productInCart ? 'disabled' : '' }}>
                            <i class="bi bi-cart-plus me-1"></i>
                            <span class="btn-text">{{ $productInCart ? 'В корзине' : 'В корзину' }}</span>
                            <span class="spinner-border spinner-border-sm d-none ms-1" role="status" aria-hidden="true"></span>
                        </button>
                    @else
                        <button class="btn btn-secondary stop-propagation" disabled>
                            Нет в наличии
                        </button>
                    @endif
                @else
                    <button class="btn btn-primary stop-propagation" data-bs-toggle="modal" data-bs-target="#userModal">
                        <i class="bi bi-box-arrow-in-right me-1"></i> В корзину
                    </button>
                @endauth
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const clickableCards = document.querySelectorAll('.clickable-card');

            clickableCards.forEach(card => {
                card.addEventListener('click', function (event) {
                    // Проверяем, был ли клик по элементу, который должен остановить переход (например, кнопка)
                    // или по его дочернему элементу.
                    let targetElement = event.target;
                    let stopPropagation = false;

                    while (targetElement && targetElement !== card) {
                        if (targetElement.classList.contains('stop-propagation') || targetElement.tagName === 'BUTTON' || targetElement.tagName === 'A') {
                            stopPropagation = true;
                            break;
                        }
                        targetElement = targetElement.parentElement;
                    }
                    // Также проверим, если кликнули непосредственно по самому элементу с классом stop-propagation
                    if (event.target.classList.contains('stop-propagation')) {
                        stopPropagation = true;
                    }


                    if (!stopPropagation) {
                        const url = card.dataset.url;
                        if (url) {
                            window.location.href = url;
                        }
                    }
                });
            });
        });
    </script>
@endpush
