@extends('layouts.app')

@section('title', 'Мой список желаний')

@section('content')
    <main>
        <div class="mt-4">
            <div class="container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('welcome') }}">Главная</a></li>
                        <li class="breadcrumb-item"><a href="#!">Магазин</a></li>
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
                            <h1 class="mb-1">Мой список желаний</h1>
                            <p>В вашем списке желаний {{ $products->total() }} товар(ов).</p>
                        </div>
                        <div>
                            <div class="table-responsive">
                                <table class="table text-nowrap table-with-checkbox">
                                    <thead class="table-light">
                                    <tr>
                                        <th></th>
                                        <th>Товар</th>
                                        <th>Цена</th>
                                        <th>Статус</th>
                                        <th></th>
                                        <th>Удалить</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse ($products as $product)
                                        <tr>
                                            <td class="align-middle">
                                                <a href="{{ route('product.show', $product->slug) }}">
                                                    <img src="{{ $product->primaryImage ? asset('storage/' . $product->primaryImage->image_url) : asset('assets/images/placeholder.png') }}"
                                                         class="icon-shape icon-xxl" alt="{{ $product->name }}" />
                                                </a>
                                            </td>
                                            <td class="align-middle">
                                                <div>
                                                    <h5 class="fs-6 mb-0">
                                                        <a href="{{ route('product.show', $product->slug) }}" class="text-inherit">{{ $product->name }}</a>
                                                    </h5>
                                                    @if($product->category)
                                                        <small>{{ $product->category->name }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                @if($product->sale_price && $product->sale_price < $product->price)
                                                    <span class="text-dark">{{ number_format($product->sale_price, 2, '.', ' ') }} ₽</span>
                                                    <span class="text-decoration-line-through text-muted">{{ number_format($product->price, 2, '.', ' ') }} ₽</span>
                                                @else
                                                    <span class="text-dark">{{ number_format($product->price, 2, '.', ' ') }} ₽</span>
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
                                                    <a href="#!" class="btn btn-primary btn-sm add-to-cart-btn" data-product-id="{{ $product->id }}">В корзину</a>
                                                @else
                                                    <div class="btn btn-dark btn-sm disabled">Нет в наличии</div>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                <a href="#!" class="text-muted wishlist-toggle-btn" data-product-id="{{ $product->id }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Удалить">
                                                    <i class="feather-icon icon-trash-2"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
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

@push('scripts')
    <script>
        // JS для добавления/удаления из избранного
        document.body.addEventListener('click', function(event) {
            let target = event.target.closest('.wishlist-toggle-btn');
            if (target) {
                event.preventDefault();
                const productId = target.dataset.productId;
                const url = `/wishlist/toggle/${productId}`; // Laravel сам подставит ID
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Просто перезагружаем страницу, чтобы обновить список
                            window.location.reload();
                        } else {
                            alert(data.message || 'Произошла ошибка');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        });
    </script>
@endpush
