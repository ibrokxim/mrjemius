@extends('layouts.app')

@section('title', 'Наш Блог')

@section('content')
    <main>
        <div class="mt-4">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('welcome') }}">{{__('main')}}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{__('blog')}}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <section class="mt-8">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h1 class="fw-bold">{{__('Blog')}}</h1>
                    </div>
                </div>
            </div>
        </section>

        <section class="mt-6 mb-lg-14 mb-8">
            <div class="container">
                {{-- Рекомендуемый пост (если есть) --}}
                @if($featuredPost)
                    <div class="row d-flex align-items-center mb-8">
                        <div class="col-12 col-md-12 col-lg-8">
                            <a href="{{ route('blog.show', $featuredPost->slug) }}">
                                <div class="img-zoom">
                                    <img src="{{ asset('storage/' . $featuredPost->featured_image_url) }}" alt="{{ $featuredPost->title }}" class="img-fluid w-100" />
                                </div>
                            </a>
                        </div>
                        <div class="col-12 col-md-12 col-lg-4">
                            <div class="ps-lg-8 mt-8 mt-lg-0">
                                <h2 class="mb-3 h3"><a href="{{ route('blog.show', $featuredPost->slug) }}" class="text-inherit">{{ $featuredPost->title }}</a></h2>
                                <p>{{ $featuredPost->excerpt }}</p>
                                <div class="d-flex justify-content-between text-muted">
                                    <span><small>{{ $featuredPost->published_at->format('d M Y') }}</small></span>
                                    <span><small>Время чтения: <span class="text-dark fw-bold">{{-- 6min --}}</span></small></span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Сетка остальных постов --}}
                <div class="row">
                    @forelse ($posts as $post)
                        <div class="col-12 col-md-6 col-lg-4 mb-10">
                            @include('components.post-card', ['post' => $post])
                        </div>
                    @empty
                        <div class="col-12">
                            <p>{{__('blog empty')}}</p>
                        </div>
                    @endforelse

                    <div class="col-12">
                        {{-- Пагинация --}}
                        {{ $posts->links() }}
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
