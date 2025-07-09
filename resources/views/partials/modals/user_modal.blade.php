<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4">
            <div class="modal-header border-0">
                <h5 class="modal-title fs-3 fw-bold" id="userModalLabel">
                    @auth
                        Личный кабинет
                    @else
                        Вход
                    @endauth
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>

            <div class="modal-body text-center">
                @auth
                    <div class="d-flex flex-column align-items-center">
                        @if(Auth::user()->telegram_photo_url)
                            <img src="{{ Auth::user()->telegram_photo_url }}" alt="Avatar" class="rounded-circle mb-3" width="80" height="80">
                        @endif
                        <h5>{{ Auth::user()->name }} @if(Auth::user()->telegram_username) {{ Auth::user()->telegram_username }} @endif</h5>
                        <p class="text-muted mb-3">Вы вошли через Telegram</p>

{{--                        <a href="{{ route('profile') }}" class="btn btn-outline-primary w-100 mb-2">Профиль</a>--}}

{{--                        <form method="POST" action="{{ route('logout') }}">--}}
{{--                            @csrf--}}
{{--                            <button class="btn btn-danger w-100">Выйти</button>--}}
{{--                        </form>--}}
                    </div>
                @else
                    <p class="mb-4">Войдите через Telegram, чтобы сохранять корзину, отслеживать заказы и пользоваться бонусами.</p>

                    <script async src="https://telegram.org/js/telegram-widget.js?22"
                            data-telegram-login="mrdjemiusuz_bot"
                            data-size="large"
                            data-userpic="true"
                            data-request-access="write"
                            data-auth-url="https://mrdjemiuszero.uz/auth/telegram/callback">
                    </script>
                @endauth
            </div>

            @guest
                <div class="modal-footer border-0 justify-content-center">
                    <small class="text-muted">Мы не получаем доступ к вашим сообщениям.</small>
                </div>
            @endguest
        </div>
    </div>
</div>
