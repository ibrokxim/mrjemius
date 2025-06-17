<!-- Модальное окно: Выбор местоположения -->
<div class="modal fade" id="locationModal" tabindex="-1" aria-labelledby="locationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body p-6">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="mb-1" id="locationModalLabel">Выберите ваше местоположение для доставки</h5>
                        <p class="mb-0 small">Введите ваш адрес, и мы уточним предложение для вашего района.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <div class="my-5">
                    <input type="search" class="form-control" placeholder="Поиск вашего района" />
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">Выберите местоположение</h6>
                    <a href="#" class="btn btn-outline-gray-400 text-muted btn-sm">Очистить все</a>
                </div>
                <div>
                    {{-- data-simplebar используется для кастомного скроллбара, убедитесь, что JS для simplebar подключен --}}
                    <div data-simplebar style="height: 300px;">
                        <div class="list-group list-group-flush">
                            {{-- Здесь должен быть динамический список локаций/городов/районов --}}
                            <a href="#" class="list-group-item d-flex justify-content-between align-items-center px-2 py-3 list-group-item-action active">
                                <span>Москва</span>
                                <span>Мин. заказ: 1000₽</span>
                            </a>
                            <a href="#" class="list-group-item d-flex justify-content-between align-items-center px-2 py-3 list-group-item-action">
                                <span>Санкт-Петербург</span>
                                <span>Мин. заказ: 1200₽</span>
                            </a>
                            <a href="#" class="list-group-item d-flex justify-content-between align-items-center px-2 py-3 list-group-item-action">
                                <span>Новосибирск</span>
                                <span>Мин. заказ: 800₽</span>
                            </a>
                            {{-- ... и так далее ... --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
