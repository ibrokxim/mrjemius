@extends('layouts.app')

@section('title', 'Перенаправление на оплату')

@section('content')
    <div class="container text-center mt-5">
        <h3>Подождите, вы будете перенаправлены на Payme...</h3>
        <p class="text-muted">Если этого не произошло автоматически, нажмите кнопку ниже:</p>
        <a href="{{ $url }}" class="btn btn-primary">Перейти к оплате</a>
    </div>

    <script>
        setTimeout(function () {
            window.location.href = @json($url);
        }, 1500); // 1.5 секунды
    </script>
@endsection
