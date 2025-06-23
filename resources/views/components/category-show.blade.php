@extends('layouts.app')

{{-- Устанавливаем заголовок страницы, используя имя категории --}}
@section('title', $category->meta_title ?? $category->name)

@section('content')
    <!-- section-->
    <div class="mt-4">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('welcome') }}">Главная</a></li>
                            <li class="breadcrumb-item"><a href="{{-- {{ route('catalog.index') }} --}}#!">Каталог</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- section -->
    <div class="mt-8 mb-lg-14 mb-8">
        <!-- container -->
        <div class="container">
            <!-- row -->
            <div class="row gx-10">
                <!-- col -->
                <aside class="col-lg-3 col-md-4 mb-6 mb-md-0">
                    <div class="offcanvas offcanvas-start offcanvas-collapse w-md-50" tabindex="-1" id="offcanvasCategory" aria-labelledby="offcanvasCategoryLabel">
                        <div class="offcanvas-header d-lg-none">
                            <h5 class="offcanvas-title" id="offcanvasCategoryLabel">Фильтры</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body ps-lg-2 pt-lg-0">
                            {{-- Включаем компонент для боковой панели с фильтрами --}}
                            @include('partials.shop-sidebar', ['allCategories' => $allCategories, 'currentCategory' => $category])
                        </div>
                    </div>
                </aside>
                <section class="col-lg-9 col-md-12">
                    <!-- card -->
                    <div class="card mb-4 bg-light border-0">
                        <!-- card body -->
                        <div class="card-body p-9">
                            <h2 class="mb-0 fs-1">{{ $category->name }}</h2>

                        </div>
                    </div>
                    <!-- list icon -->
                    <form id="filter_sort_form" action="{{ url()->current() }}" method="GET">
                        {{-- Передаем существующие фильтры в скрытых полях, чтобы не потерять их при смене сортировки/пагинации --}}
                        <input type="hidden" name="price_from" value="{{ request('price_from') }}">
                        <input type="hidden" name="price_to" value="{{ request('price_to') }}">
                        <input type="hidden" name="rating" value="{{ request('rating') }}">

                        <div class="d-lg-flex justify-content-between align-items-center">
                            <div class="mb-3 mb-lg-0">
                                <p class="mb-0">
                                    <span class="text-dark">{{ $products->total() }}</span> товаров найдено
                                </p>
                            </div>
                            <div class="d-md-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="ms-2 d-lg-none">
                                        <a class="btn btn-outline-gray-400 text-muted" data-bs-toggle="offcanvas" href="#offcanvasCategory" role="button" aria-controls="offcanvasCategory">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-filter me-2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                                            Фильтры
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex mt-2 mt-lg-0">
                                    <div class="me-2 flex-grow-1">
                                        <select class="form-select" name="per_page" onchange="this.form.submit()">
                                            <option value="12" @selected(request('per_page', 12) == 12)>Показывать: 12</option>
                                            <option value="24" @selected(request('per_page') == 24)>24</option>
                                            <option value="36" @selected(request('per_page') == 36)>36</option>
                                        </select>
                                    </div>
                                    <div>
                                        <select class="form-select" name="sort" onchange="this.form.submit()">
                                            <option value="newest" @selected(request('sort', 'newest') == 'newest')>Сортировать: Новинки</option>
                                            <option value="price-asc" @selected(request('sort') == 'price-asc')>Цена: по возрастанию</option>
                                            <option value="price-desc" @selected(request('sort') == 'price-desc')>Цена: по убыванию</option>
                                            <option value="rating-desc" @selected(request('sort') == 'rating-desc')>Рейтинг</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Сетка продуктов -->
                    <div class="row g-4 row-cols-xl-4 row-cols-lg-3 row-cols-2 row-cols-md-2 mt-2">
                        @forelse($products as $product)
                            <div class="col">
                                @include('components.product-card', ['product' => $product])
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info">В этой категории пока нет товаров или они не соответствуют вашим критериям фильтрации.</div>
                            </div>
                        @endforelse
                    </div>

                    <div class="row mt-8">
                        <div class="col">
                            {{ $products->withQueryString()->links() }}
                        </div>
                    </div>
                </section>
            </div>
            @if($category->description)
                <div class="row mt-8">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body p-4 p-md-5">
                                {!! $category->description !!}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Скрипты для слайдера цен и других элементов этой страницы --}}
    <script src="{{ asset('assets/libs/nouislider/dist/nouislider.min.js') }}"></script>
    <script src="{{ asset('assets/libs/wnumb/wNumb.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var priceRange = document.getElementById('priceRange');
            if (priceRange) {
                var priceMinInput = document.getElementById('price_from');
                var priceMaxInput = document.getElementById('price_to');
                var priceRangeValue = document.getElementById('priceRange-value');

                var startMin = parseInt(priceMinInput.value) || 0;
                var startMax = parseInt(priceMaxInput.value) || 10000;

                noUiSlider.create(priceRange, {
                    start: [startMin, startMax],
                    connect: true,
                    range: { 'min': 0, 'max': 10000 },
                    step: 100,
                    format: wNumb({ decimals: 0 })
                });

                priceRange.noUiSlider.on('update', function (values, handle) {
                    var formattedValues = [
                        wNumb({decimals: 0, thousand: ' '}).to(parseInt(values[0])),
                        wNumb({decimals: 0, thousand: ' '}).to(parseInt(values[1]))
                    ];
                    priceRangeValue.innerHTML = formattedValues.join(' - ') + ' ₽';
                });

                priceRange.noUiSlider.on('change', function (values, handle) {
                    priceMinInput.value = values[0];
                    priceMaxInput.value = values[1];
                });
            }
        });
    </script>
@endpush
