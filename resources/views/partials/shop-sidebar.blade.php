<form action="{{ url()->current() }}" method="GET">
    {{-- Передаем существующие параметры сортировки/пагинации, чтобы не потерять их при применении фильтров --}}
    <input type="hidden" name="sort" value="{{ request('sort') }}">
    <input type="hidden" name="per_page" value="{{ request('per_page') }}">

    <div class="mb-8">
        <h5 class="mb-3">{{__('Categories')}}</h5>
        <ul class="nav nav-category" id="categoryCollapseMenu">
            @foreach($allCategories as $cat)
                <li class="nav-item border-bottom w-100">
                    <a href="{{ route('category.show', $cat->slug) }}" class="nav-link {{ request()->is('category/'.$cat->slug) || $cat->children->contains($currentCategory) ? '' : 'collapsed' }}">
                        {{ $cat->name }}
                        @if($cat->children->isNotEmpty())
                            {{-- Иконка, если есть дочерние категории --}}
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="mb-8">
        <h5 class="mb-3">{{__('Price')}}</h5>
        <div class="d-flex align-items-center gap-2">
            <input type="number" name="price_from" class="form-control" placeholder="{{__('ot')}}" min="0" value="{{ request('price_from') }}">
            <span>–</span>
            <input type="number" name="price_to" class="form-control" placeholder="{{__('do')}}" min="0" value="{{ request('price_to') }}">
        </div>
    </div>

    <div class="mb-8">
        <h5 class="mb-3">{{__('Rating')}}</h5>
        <div>
            @for ($i = 4; $i >= 1; $i--)
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="rating" value="{{ $i }}" id="rating{{ $i }}" @checked(request('rating') == $i)>
                    <label class="form-check-label d-flex align-items-center" for="rating{{ $i }}">
                        @for ($j = 1; $j <= 5; $j++)
                            <i class="bi bi-star{{ $j <= $i ? '-fill' : '' }} text-warning"></i>
                        @endfor
                        <span class="ms-1 small text-muted">{{__('And more')}}</span>
                    </label>
                </div>
            @endfor
        </div>
    </div>

    <div class="d-grid mb-2">
        <button type="submit" class="btn btn-primary">{{__('Apply filters')}}</button>
    </div>
    <div class="d-grid">
        <a href="{{ url()->current() }}" class="btn btn-outline-secondary">{{__('Clean filters')}}</a>
    </div>
</form>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var priceRange = document.getElementById('priceRangeSidebar');
            if (priceRange && !priceRange.noUiSlider) {
                var priceMinInput = document.getElementById('price_from_sidebar');
                var priceMaxInput = document.getElementById('price_to_sidebar');
                var priceRangeValue = document.getElementById('priceRange-value'); // оставь, если нужен

                var startMin = priceMinInput && priceMinInput.value !== '' ? parseInt(priceMinInput.value) : 0;
                var startMax = priceMaxInput && priceMaxInput.value !== '' ? parseInt(priceMaxInput.value) : 300000;

                noUiSlider.create(priceRange, {
                    start: [startMin, startMax],
                    connect: true,
                    range: { 'min': 0, 'max': 300000 },
                    step: 10000,
                    format: wNumb({ decimals: 0 })
                });

                priceRange.noUiSlider.on('update', function (values) {
                    if (priceRangeValue) {
                        priceRangeValue.innerHTML = values.join(' - ') + ' сум';
                    }
                });

                priceRange.noUiSlider.on('change', function (values) {
                    priceMinInput.value = values[0];
                    priceMaxInput.value = values[1];
                });
            }
        });
    </script>
@endpush

