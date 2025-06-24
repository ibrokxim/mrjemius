<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4">
            <div class="modal-header border-0">
                <h5 class="modal-title fs-3 fw-bold" id="userModalLabel">Вход или Регистрация</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body text-center">
                <p class="mb-4">Войдите через Telegram, чтобы сохранять корзину, отслеживать заказы и пользоваться бонусами.</p>

                {{-- Скрипт для кнопки Telegram Login Widget --}}
                <script async src="https://telegram.org/js/telegram-widget.js?22"
                        data-telegram-login="mrjemius_bot"
                        data-size="large"
                        data-userpic="true"
                        data-request-access="write"
                        data-auth-url="https://mrdjemiuszero.uz/auth/telegram/callback">
                </script>

            </div>
            <div class="modal-footer border-0 justify-content-center">
                <small class="text-muted">Мы не получаем доступ к вашим сообщениям.</small>
            </div>
        </div>
    </div>
</div>
