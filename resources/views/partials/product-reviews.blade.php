<div class="my-8">
    <div class="row">
        <div class="col-md-4">
            <div class="me-lg-12 mb-6 mb-md-0">
                <div class="mb-5">
                    <h4 class="mb-3">Отзывы клиентов</h4>
                    {{-- Логика отображения общего рейтинга --}}
                </div>
                <div class="d-grid">
                    <h4>Оцените этот товар</h4>
                    <p class="mb-0">Поделитесь своим мнением с другими покупателями.</p>
                    <a href="#" class="btn btn-outline-gray-400 mt-4 text-muted">Написать отзыв</a>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="mb-10">
                <div class="d-flex justify-content-between align-items-center mb-8">
                    <div><h4>Отзывы</h4></div>
                    {{-- Сортировка отзывов --}}
                </div>
                @forelse($reviews as $review)
                    <div class="d-flex border-bottom pb-6 mb-6">
                        <img src="{{-- {{ $review->user->avatar_url ?? asset('assets/images/avatar/avatar-placeholder.jpg') }} --}}" alt="" class="rounded-circle avatar-lg" />
                        <div class="ms-5">
                            <h6 class="mb-1">{{ $review->user->name ?? 'Анонимный пользователь' }}</h6>
                            <p class="small"><span class="text-muted">{{ $review->created_at->format('d M Y') }}</span></p>
                            <div class="mb-2">
                                <small class="text-warning">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                                    @endfor
                                </small>
                            </div>
                            <p>{{ $review->comment }}</p>
                        </div>
                    </div>
                @empty
                    <p>Для этого товара еще нет отзывов. Будьте первым!</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
