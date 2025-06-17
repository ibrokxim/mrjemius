<!-- Модальное окно: Пользователь (Регистрация/Вход) -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4">
            <div class="modal-header border-0">
                <h5 class="modal-title fs-3 fw-bold" id="userModalLabel">Регистрация</h5> {{-- Или "Вход" / "Авторизация" --}}
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                {{-- Форма регистрации (или входа) - используйте Laravel-формы или стандартный HTML --}}
                {{-- <form method="POST" action="{{ route('register') }}" class="needs-validation" novalidate> --}}
                <form class="needs-validation" novalidate> {{-- Уберите action и method, если обрабатываете через JS/AJAX --}}
                    {{-- @csrf --}} {{-- CSRF токен для Laravel форм --}}
                    <div class="mb-3">
                        <label for="userModalFullName" class="form-label">Имя</label>
                        <input type="text" class="form-control" id="userModalFullName" name="name" placeholder="Введите ваше имя" required />
                        <div class="invalid-feedback">Пожалуйста, введите имя.</div>
                    </div>
                    <div class="mb-3">
                        <label for="userModalEmail" class="form-label">Email адрес</label>
                        <input type="email" class="form-control" id="userModalEmail" name="email" placeholder="Введите email адрес" required />
                        <div class="invalid-feedback">Пожалуйста, введите корректный email.</div>
                    </div>
                    <div class="mb-3">
                        <label for="userModalPassword" class="form-label">Пароль</label>
                        <input type="password" class="form-control" id="userModalPassword" name="password" placeholder="Введите пароль" required />
                        <div class="invalid-feedback">Пожалуйста, введите пароль.</div>
                    </div>
                    {{-- Для регистрации можно добавить поле "Подтверждение пароля" --}}
                    {{-- <div class="mb-3">
                        <label for="userModalPasswordConfirm" class="form-label">Подтвердите пароль</label>
                        <input type="password" class="form-control" id="userModalPasswordConfirm" name="password_confirmation" placeholder="Подтвердите пароль" required />
                        <div class="invalid-feedback">Пароли не совпадают.</div>
                    </div> --}}
                    <small class="form-text">
                        Регистрируясь, вы соглашаетесь с нашими
                        <a href="{{-- {{ route('terms') }} --}}#!">Условиями обслуживания</a>
                        и
                        <a href="{{-- {{ route('privacy') }} --}}#!">Политикой конфиденциальности</a>.
                    </small>

                    <div class="d-grid mt-3"> {{-- Для кнопки на всю ширину --}}
                        <button type="submit" class="btn btn-primary">Зарегистрироваться</button> {{-- Или "Войти" --}}
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                Уже есть аккаунт?
                <a href="{{-- {{ route('login') }} --}}#" data-bs-target="#loginModal" data-bs-toggle="modal" data-bs-dismiss="modal">Войти</a> {{-- Переключатель на модальное окно входа, если оно отдельное --}}
            </div>
        </div>
    </div>
</div>
