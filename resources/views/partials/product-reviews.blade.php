{{-- Этот файл теперь принимает переменные: $reviews, $reviewsCount, $avgRating, $ratingDistribution, и $product (для формы) --}}
<div class="my-8">
    <div class="row">
        {{-- Левая колонка со статистикой --}}
        <div class="col-md-4">
            <div class="me-lg-12 mb-6 mb-md-0">
                <div class="mb-5">
                    <h4 class="mb-3">Отзывы клиентов</h4>
                    @if($reviewsCount > 0)
                        <span>
                        <small class="text-warning">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star{{ $i <= round($avgRating) ? '-fill' : '' }}"></i>
                            @endfor
                        </small>
                        <span class="ms-3">{{ number_format($avgRating, 1) }} из 5</span>
                        <small class="ms-3">{{ $reviewsCount }} {{ trans_choice('отзыв|отзыва|отзывов', $reviewsCount) }}</small>
                    </span>
                    @endif
                </div>

                {{-- Прогресс-бары с распределением оценок --}}
                @if($reviewsCount > 0)
                    <div class="mb-8">
                        @foreach($ratingDistribution as $rating => $percentage)
                            <div class="d-flex align-items-center mb-2">
                                <div class="text-nowrap me-3 text-muted">
                                    <span class="d-inline-block align-middle text-muted">{{ $rating }}</span>
                                    <i class="bi bi-star-fill ms-1 small text-warning"></i>
                                </div>
                                <div class="w-100">
                                    <div class="progress" style="height: 6px">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <span class="text-muted ms-3">{{ $percentage }}%</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Кнопка для добавления отзыва --}}
                <div class="d-grid">
                    @auth
                        <h4>Оцените этот товар</h4>
                        <p class="mb-0">Поделитесь своим мнением с другими покупателями.</p>
                        <button id="write-review-btn" class="btn btn-outline-gray-400 mt-4 text-muted">Написать отзыв</button>
                    @else
                        <h4>Оставьте свой отзыв</h4>
                        <p class="mb-0">Пожалуйста, <a href="#!" data-bs-toggle="modal" data-bs-target="#userModal">войдите</a>, чтобы оставить отзыв.</p>
                    @endauth
                </div>
            </div>
        </div>

        {{-- Правая колонка со списком отзывов и формой --}}
        <div class="col-md-8">
            <div class="mb-10">
                <div class="d-flex justify-content-between align-items-center mb-8">
                    <div><h4>Отзывы</h4></div>
                    {{-- Здесь можно добавить сортировку отзывов --}}
                </div>
                @forelse($reviews as $review)
                    <div class="d-flex border-bottom pb-6 mb-6">
                        <img src="{{ $review->user->telegram_photo_url }}" alt="" class="rounded-circle avatar-lg" />
                        <div class="ms-5">
                            <h6 class="mb-1">{{ $review->user->name ?? 'Аноним' }}</h6>
                            <p class="small"><span class="text-muted">{{ $review->created_at->format('d M Y') }}</span></p>
                            <div class="mb-2">
                                <small class="text-warning">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}"></i>
                                    @endfor
                                </small>
                            </div>
                            <p>{!! nl2br(e($review->comment)) !!}</p> {{-- nl2br для переносов строк, e() для безопасности --}}
                        </div>
                    </div>
                @empty
                    <p>Для этого товара еще нет отзывов. Будьте первым!</p>
                @endforelse
            </div>

            {{-- СКРЫТАЯ ФОРМА ДОБАВЛЕНИЯ ОТЗЫВА --}}
            @auth
                <div id="review-form-container" class="mt-5" style="display: none;">
                    <hr>
                    <h3 class="mb-5">Написать отзыв</h3>
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    <form action="{{ route('reviews.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="border-bottom py-4 mb-4">
                            <h4 class="mb-3">Общая оценка</h4>
                            <div id="rating-stars">
                                @for ($i = 5; $i >= 1; $i--)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="rating" id="rating_{{ $i }}" value="{{ $i }}" required {{ old('rating') == $i ? 'checked' : '' }}>
                                        <label class="form-check-label" for="rating_{{ $i }}">{{ $i }} <i class="bi bi-star-fill text-warning"></i></label>
                                    </div>
                                @endfor
                            </div>
                        </div>
                        <div class="py-4 mb-4">
                            <h5>Напишите свой отзыв</h5>
                            <textarea class="form-control" name="comment" rows="3" placeholder="Что вам понравилось или не понравилось?" required>{{ old('comment') }}</textarea>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Отправить отзыв</button>
                        </div>
                    </form>
                </div>
            @endauth
        </div>
    </div>
</div>
