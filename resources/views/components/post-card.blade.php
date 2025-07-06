@props(['post'])

<div>
    <div class="mb-4">
        <a href="{{ route('blog.show', $post->slug) }}">
            <div class="img-zoom">
                <img src="{{ asset('storage/' . $post->featured_image_url) }}" alt="{{ $post->title }}" class="img-fluid w-100" />
            </div>
        </a>
    </div>
    @if($post->categories->isNotEmpty())
        <div class="mb-3">
            <a href="#!">{{ $post->categories->first()->name }}</a>
        </div>
    @endif
    <div>
        <h2 class="h5"><a href="{{ route('blog.show', $post->slug) }}" class="text-inherit">{{ $post->title }}</a></h2>
        <p>{{ $post->excerpt }}</p>
        <div class="d-flex justify-content-between text-muted mt-4">
            <span><small>{{ $post->published_at->format('d M Y') }}</small></span>
{{--            <span><small>Время чтения: <span class="text-dark fw-bold">--}}{{-- 8min --}}{{--</span></small></span>--}}
        </div>
    </div>
</div>
