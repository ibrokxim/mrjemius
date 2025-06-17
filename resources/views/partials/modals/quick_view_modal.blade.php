<!-- Модальное окно: Быстрый просмотр товара -->
<div class="modal fade" id="quickViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body p-8">
                <div class="position-absolute top-0 end-0 me-3 mt-3">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                {{-- Содержимое будет загружаться сюда через JavaScript --}}
                <div id="quickViewModalContent">
                    {{-- Примерная структура (заполняется динамически) --}}
                    {{--
                    <div class="row">
                        <div class="col-lg-6">
                            <!-- Слайдер изображений -->
                            <div class="product productModal">
                                <div class="zoom" onmousemove="zoom(event)" style="background-image: url(ПУТЬ_К_ИЗОБРАЖЕНИЮ_1)">
                                    <img src="ПУТЬ_К_ИЗОБРАЖЕНИЮ_1" alt="Название товара" />
                                </div>
                                <!-- ... другие изображения для слайдера ... -->
                            </div>
                            <div class="product-tools">
                                <div class="thumbnails row g-3">
                                    <!-- ... миниатюры ... -->
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="ps-lg-8 mt-6 mt-lg-0">
                                <a href="#!" class="mb-4 d-block text-muted">Категория товара</a>
                                <h2 class="mb-1 h1">Название товара</h2>
                                <div class="mb-4">
                                    <small class="text-warning">
                                        <!-- ... звезды рейтинга ... -->
                                    </small>
                                    <a href="#" class="ms-2">(_КОЛ-ВО_ ОТЗЫВОВ_ отзыва(ов))</a>
                                </div>
                                <div class="fs-4">
                                    <span class="fw-bold text-dark">_ЦЕНА_ руб.</span>
                                    <span class="text-decoration-line-through text-muted">_СТАРАЯ_ЦЕНА_ руб.</span>
                                    <span><small class="fs-6 ms-2 text-danger">_СКИДКА_% Off</small></span>
                                </div>
                                <hr class="my-6" />
                                <div class="mb-4">
                                    <!-- ... варианты (размеры, цвета) ... -->
                                </div>
                                <div>
                                    <div class="input-group input-spinner">
                                        <!-- ... счетчик количества ... -->
                                    </div>
                                </div>
                                <div class="mt-3 row justify-content-start g-2 align-items-center">
                                    <div class="col-lg-4 col-md-5 col-6 d-grid">
                                        <button type="button" class="btn btn-primary">
                                            <i class="feather-icon icon-shopping-bag me-2"></i>В корзину
                                        </button>
                                    </div>
                                    <!-- ... кнопки сравнения, в избранное ... -->
                                </div>
                                <hr class="my-6" />
                                <div>
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr><td>Код товара:</td><td>_АРТИКУЛ_</td></tr>
                                            <tr><td>Наличие:</td><td>_В НАЛИЧИИ_ / _НЕТ В НАЛИЧИИ_</td></tr>
                                            <tr><td>Тип:</td><td>_ТИП_ТОВАРА_</td></tr>
                                            <tr><td>Доставка:</td><td><small>_ИНФО_О_ДОСТАВКЕ_</small></td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    --}}
                    <p class="text-center py-5">Загрузка информации о товаре...</p> {{-- Заглушка --}}
                </div>
            </div>
        </div>
    </div>
</div>
