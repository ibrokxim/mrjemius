@extends('layouts.app')

@section('title', $post->title)

@section('content')
    <main>
        <div class="mt-4">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <!-- breadcrumb -->
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('welcome') }}">Главная</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('blog.index') }}">Блог</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ $post->title }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <section class="my-lg-14 my-8">
            <div class="container">
                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <!-- text -->
                        <div class="mb-5">
                            @if($post->categories->isNotEmpty())
                                <div class="mb-3 text-center"><a href="#!">{{ $post->categories->first()->name }}</a></div>
                            @endif
                            <h1 class="fw-bold text-center">{{ $post->title }}</h1>
                            <div class="d-flex justify-content-center text-muted mt-4">
                                <span class="me-2"><small>{{ $post->published_at->format('d M Y') }}</small></span>
                                <span><small>Автор: <span class="text-dark fw-bold">{{ $post->author->name ?? 'Администратор' }}</span></small></span>
                            </div>
                        </div>
                        <!-- img -->
                        <div class="mb-8">
                            <img src="{{ asset('storage/' . $post->featured_image_url) }}" alt="{{ $post->title }}" class="img-fluid rounded" />
                        </div>

                        <div>
                            {{-- Выводим контент поста, который был сохранен из RichEditor --}}
                            <div class="mb-4">
                                {!! $post->content !!}
                            </div>
                        </div>

                        <hr class="mt-8 mb-5" />
                        <div class="d-flex justify-content-between align-items-center mb-5">
                            <div class="d-flex align-items-center">
                                <img src="{{-- {{ $post->author->avatar_url ?? asset('assets/images/avatar/avatar-placeholder.jpg') }} --}}" alt="" class="rounded-circle avatar-md" />
                                <div class="ms-2 lh-1">
                                    <h5 class="mb-0">{{ $post->author->name ?? 'Администратор' }}</h5>
                                    <span class="text-primary small">{{-- Marketing Manager --}}</span>
                                </div>
                            </div>
                            <div>
                                <span class="ms-2 text-muted">Поделиться</span>
                                {{-- Здесь можно добавить кнопки "поделиться", как мы делали для продукта --}}
                                <a href="#" class="ms-2 text-muted"><i class="bi bi-facebook fs-6"></i></a>
                                <a href="#" class="ms-2 text-muted"><i class="bi bi-twitter fs-6"></i></a>
                                <a href="#" class="ms-2 text-muted"><i class="bi bi-linkedin fs-6"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Секция "Похожие посты" --}}
        @if(isset($relatedPosts) && $relatedPosts->isNotEmpty())
            <section class="my-lg-14 my-14">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <h3>Похожие посты</h3>
                        </div>
                    </div>
                    <div class="row g-4 row-cols-lg-4 row-cols-2 row-cols-md-2 mt-2">
                        @foreach($relatedPosts as $relatedPost)
                            <div class="col">
                                @include('components.post-card', ['post' => $relatedPost])
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </main>
@endsection
