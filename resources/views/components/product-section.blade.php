@props([
    'title', // Заголовок секции
    'products', // Коллекция продуктов для отображения
    'subtitle' => null, // Опциональный подзаголовок
    'isSlider' => false // Будет ли это слайдер или сетка
])

<section class="my-lg-14 my-8">
    <div class="container">
        <div class="row">
            <div class="col-12 mb-6">
                <h3 class="mb-0">{{ $title }}</h3>
                @if($subtitle)
                    <p class="mb-0">{{ $subtitle }}</p>
                @endif
            </div>
        </div>

        @if(isset($products) && $products->isNotEmpty())
            {{-- Определяем классы для контейнера: слайдер или обычная сетка --}}
            <div class="{{ $isSlider ? 'product-slider' : 'row g-4 row-cols-lg-5 row-cols-2 row-cols-md-3' }}">
                @foreach($products as $product)
                    <div class="{{ $isSlider ? 'item' : 'col' }}">
                        @include('components.product-card', ['product' => $product])
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-light">В данный момент нет товаров для отображения в этой секции.</div>
        @endif
    </div>
</section><?php
