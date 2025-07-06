@extends('layouts.app')

@section('title', 'Результаты поиска по запросу: ' . e($searchQuery))

@section('content')
    <div class="mt-4">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('welcome') }}">{{__('main')}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{__('search')}}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="mt-8 mb-lg-14 mb-8">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="mb-4">
                        <h2>{{__('search results')}}"{{ e($searchQuery) }}"</h2>
                        <p class="mb-0">
                            <span class="text-dark">{{ $products->total() }}</span> {{__('products find')}}
                        </p>
                    </div>

                    <!-- Сетка продуктов -->
                    <div class="row g-4 row-cols-xl-5 row-cols-lg-4 row-cols-2 row-cols-md-3">
                        @forelse($products as $product)
                            <div class="col">
                                @include('components.product-card', ['product' => $product])
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-warning">{{__('Search text')}}</div>
                            </div>
                        @endforelse
                    </div>

                    <!-- Пагинация -->
                    <div class="row mt-8">
                        <div class="col">
                            {{-- withQueryString() добавит ?query=... к ссылкам пагинации --}}
                            {{ $products->withQueryString()->links() }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
