@extends('layouts.app')

@section('title', 'Мой список желаний')

@section('content')
    <main>
        <div class="mt-4">
            <div class="container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('welcome') }}">{{__('main')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('My wishlist')}}</li>
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
                                    <h1 class="mb-1">{{__('My wishlist')}}</h1>
                                    <p>{{__('in wishlist')}} {{ $products->total() }} {{__('tovars')}}</p>
                                </div>
                                @if($products->count() > 0)
                                    <div>
                                        <button class="btn btn-success" id="moveAllToCartBtn">
                                            <i class="bi bi-cart-plus me-2"></i>{{__('all in cart')}}
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
                                        <th>{{__('tovar')}}</th>
                                        <th>{{__('cost')}}</th>
                                        <th>{{__('status')}}</th>
                                        <th>{{__('actions')}}</th>
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
                                                    <span class="text-dark">{{ number_format($product->sale_price, 0, '.', ' ') }} </span>
                                                    <span class="text-decoration-line-through text-muted ms-1">{{ number_format($product->price, 0, '.', ' ') }} </span>
                                                @else
                                                    <span class="text-dark">{{ number_format($product->price, 0, '.', ' ') }} </span>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                @if($product->stock_quantity > 0)
                                                    <span class="badge bg-success">{{__('In sklad')}}</span>
                                                @else
                                                    <span class="badge bg-danger">{{__('No in sklad')}}</span>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                @if($product->stock_quantity > 0)
                                                    <button class="btn btn-primary btn-sm add-to-cart-btn" data-product-id="{{ $product->id }}">
                                                        {{__("Add to cart")}}</button>
                                                @else
                                                    <button class="btn btn-dark btn-sm" disabled>{{__('No in sklad')}}</button>
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
                                                {{__('empty wishlist')}}
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
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // --- 1. Обработчик для удаления из избранного (кнопка с корзиной) ---
            document.querySelectorAll('.wishlist-toggle-btn').forEach(button => {
                button.addEventListener('click', async function() {
                    const productId = this.dataset.productId;
                    const row = document.getElementById(`wishlist-row-${productId}`);

                    const urlTemplate = "{{ route('wishlist.toggle', ['product' => ':productId']) }}";
                    const finalUrl = urlTemplate.replace(':productId', productId);

                    try {
                        const response = await fetch(finalUrl, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            }
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            console.log(data.message);
                            // Плавно удаляем строку из таблицы
                            if (row) {
                                row.style.transition = 'opacity 0.5s ease';
                                row.style.opacity = '0';
                                setTimeout(() => row.remove(), 500);
                            }
                            // Здесь можно также обновить счетчик товаров в "p"
                        } else {
                            console.error('Ошибка при удалении из избранного:', data.message);
                        }
                    } catch (error) {
                        console.error('Критическая ошибка:', error);
                    }
                });
            });

            // --- 2. Обработчик для добавления ОДНОГО товара в корзину ---
            document.querySelectorAll('.add-to-cart-btn').forEach(button => {
                button.addEventListener('click', async function() {
                    const currentButton = this;
                    const productId = currentButton.dataset.productId;
                    const urlTemplate = "{{ route('cart.add', ['product' => ':productId']) }}";
                    const finalUrl = urlTemplate.replace(':productId', productId);

                    currentButton.disabled = true;
                    currentButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

                    try {
                        const response = await fetch(finalUrl, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({ quantity: 1 })
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            console.log(data.message);
                            currentButton.classList.remove('btn-primary');
                            currentButton.classList.add('btn-success');
                            currentButton.innerHTML = 'В корзине';
                            // Динамическое обновление счетчика в шапке
                            updateCartCount(data.cart_count);
                        } else {
                            console.error('Ошибка при добавлении в корзину:', data.message);
                            currentButton.disabled = false;
                            currentButton.innerHTML = 'В корзину';
                        }
                    } catch (error) {
                        console.error('Критическая ошибка:', error);
                        currentButton.disabled = false;
                        currentButton.innerHTML = 'В корзину';
                    }
                });
            });

            // --- 3. Обработчик для кнопки "Все в корзину" ---
            const moveAllToCartBtn = document.getElementById('moveAllToCartBtn');
            if (moveAllToCartBtn) {
                moveAllToCartBtn.addEventListener('click', async function() {
                    const currentButton = this;
                    const url = "{{ route('cart.move.from.wishlist') }}";

                    currentButton.disabled = true;
                    currentButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Перемещаем...';

                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            }
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            console.log(data.message);
                            // Перезагружаем страницу, чтобы показать изменения (пустой список желаний и обновленную корзину)
                            window.location.reload();
                        } else {
                            console.error('Ошибка при перемещении:', data.message);
                            currentButton.disabled = false;
                            currentButton.innerHTML = '<i class="bi bi-cart-plus me-2"></i>Все в корзину';
                        }
                    } catch (error) {
                        console.error('Критическая ошибка:', error);
                        currentButton.disabled = false;
                        currentButton.innerHTML = '<i class="bi bi-cart-plus me-2"></i>Все в корзину';
                    }
                });
            }

            // --- Вспомогательная функция для обновления счетчика в шапке ---
            function updateCartCount(count) {
                const cartCountElement = document.getElementById('cart-count');
                if (cartCountElement) {
                    cartCountElement.textContent = count;
                    if (count > 0) {
                        cartCountElement.style.display = 'inline-block';
                    } else {
                        cartCountElement.style.display = 'none';
                    }
                }
            }
        });
    </script>
@endpush
