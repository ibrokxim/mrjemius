@foreach($products as $product)
    <div class="col">
        @include('components.product-card', ['product' => $product])
    </div>
@endforeach
