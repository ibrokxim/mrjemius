@extends('layouts.app')

@section('title', 'Мой список желаний')

@section('content')
    <main>
        <div class="mt-4">
            <div class="container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('welcome') }}">Главная</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Мой список желаний</li>
                    </ol>
                </nav>
            </div>
        </div>

        <section class="mt-8 mb-14">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="mb-8">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h1 class="mb-1">Мой список желаний</h1>
                                    <p>В вашем списке желаний {{ $products->total() }} товар(ов).</p>
                                </div>
                                @if($products->count() > 0)
                                    <div>
                                        <button class="btn btn-success" id="moveAllToCartBtn">
                                            <i class="bi bi-cart-plus me-2"></i>Все в корзину
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div>
                            <div class="table-responsive">
                                <table class="table text-nowrap">
                                    <thead class="table-light">
                                    <tr>
                                        <th>Товар</th>
                                        <th>Цена</th>
                                        <th>Статус</th>
                                        <th>Действия</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse ($products as $product)
                                        <tr id="wishlist-row-{{ $product->id }}">
                                            <td class="align-middle">
                                                <a href="{{ route('product.show', $product->slug) }}" class="d-flex align-items-center">
                                                    <img src="{{ $product->primaryImage ? asset('storage/' . $product->primaryImage->image_url) : asset('assets/images/placeholder.png') }}"
                                                         class="icon-shape icon-xxl" alt="{{ $product->name }}" />
                                                    <div class="ms-3">
                                                        <h5 class="fs-6 mb-0 text-inherit">{{ $product->name }}</h5>
                                                        @if($product->category)
                                                            <small>{{ $product->category->name }}</small>
                                                        @endif
                                                    </div>
                                                </a>
                                            </td>
                                            <td class="align-middle">
                                                @if($product->sale_price && $product->sale_price < $product->price)
                                                    <span class="text-dark">{{ number_format($product->sale_price, 0, '.', ' ') }} ₽</span>
                                                    <span class="text-decoration-line-through text-muted ms-1">{{ number_format($product->price, 0, '.', ' ') }} ₽</span>
                                                @else
                                                    <span class="text-dark">{{ number_format($product->price, 0, '.', ' ') }} ₽</span>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                @if($product->stock_quantity > 0)
                                                    <span class="badge bg-success">В наличии</span>
                                                @else
                                                    <span class="badge bg-danger">Нет в наличии</span>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                @if($product->stock_quantity > 0)
                                                    <button class="btn btn-primary btn-sm add-to-cart-btn" data-product-id="{{ $product->id }}">В корзину</button>
                                                @else
                                                    <button class="btn btn-dark btn-sm" disabled>Нет в наличии</button>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                <button class="btn btn-link text-muted wishlist-toggle-btn" data-product-id="{{ $product->id }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Удалить">
                                                    <i class="feather-icon icon-trash-2"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5">
                                                Ваш список желаний пуст.
                                            </td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4">
                                {{ $products->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
