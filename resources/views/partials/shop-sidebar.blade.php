<form action="{{ url()->current() }}" method="GET">
    {{-- Передаем существующие параметры сортировки/пагинации, чтобы не потерять их при применении фильтров --}}
    <input type="hidden" name="sort" value="{{ request('sort') }}">
    <input type="hidden" name="per_page" value="{{ request('per_page') }}">

    <div class="mb-8">
        <h5 class="mb-3">Категории</h5>
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
        <h5 class="mb-3">Цена</h5>
        <div>
            <div id="priceRange" class="mb-3"></div>
            <input type="hidden" name="price_from" id="price_from" value="{{ request('price_from', 0) }}">
            <input type="hidden" name="price_to" id="price_to" value="{{ request('price_to', 10000) }}">
            <small class="text-muted">Цена:</small> <span id="priceRange-value" class="small"></span>
        </div>
    </div>

    <div class="mb-8">
        <h5 class="mb-3">Рейтинг</h5>
        <div>
            @for ($i = 4; $i >= 1; $i--)
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="rating" value="{{ $i }}" id="rating{{ $i }}" @checked(request('rating') == $i)>
                    <label class="form-check-label" for="rating{{ $i }}">
                        @for ($j = 1; $j <= 5; $j++)
                            <i class="bi bi-star{{ $j <= $i ? '-fill' : '' }} text-warning"></i>
                        @endfor
                        <span class="ms-1 small">и выше</span>
                    </label>
                </div>
            @endfor
        </div>
    </div>

    <div class="d-grid mb-3">
        <button type="submit" class="btn btn-primary">Применить фильтры</button>
    </div>
    <div class="d-grid">
        <a href="{{ url()->current() }}" class="btn btn-outline-gray-400">Сбросить все фильтры</a>
    </div>
</form>
