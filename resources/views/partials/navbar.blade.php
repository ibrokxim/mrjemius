<!-- navbar -->
<div class="border-bottom">
    <div class="bg-light py-1">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-12 text-center text-md-start"><span>Супер скидки - Экономьте больше с купонами</span></div>
                <div class="col-6 text-end d-none d-md-block">
                    {{-- Логика выбора языка (можно сделать компонентом) --}}
                    <div class="dropdown selectBox">
                        <a class="dropdown-toggle selectValue text-reset" href="javascript:void(0)" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="me-2">
                               {{-- SVG для текущего языка --}}
                            </span>
                            Русский {{-- Или {{ Config::get('languages.' . App::getLocale()) }} --}}
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{-- {{ route('setlocale', 'en') }} --}}">Русский</a></li>
                            <li><a class="dropdown-item" href="{{-- {{ route('setlocale', 'uz') }} --}}">O'zbek</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="py-5">
        <div class="container">
            <div class="row w-100 align-items-center gx-lg-2 gx-0">
                <div class="col-xxl-2 col-lg-3 col-md-6 col-5">
                    <a class="navbar-brand d-none d-lg-block" href="{{ route('welcome') }}"> {{-- Пример использования route() --}}
                        <img src="{{ asset('assets/images/logo/freshcart-logo.svg') }}" alt="Логотип FreshCart" />
                    </a>
{{-- ... и так далее для остальной части шапки ... --}}
