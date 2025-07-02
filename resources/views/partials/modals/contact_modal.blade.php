<div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="contactModalLabel">Обратная связь</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Оставьте ваши контакты, и мы скоро свяжемся с вами!</p>
                {{-- Форма будет отправляться через AJAX --}}
                <form id="contact-form">
                    <div class="mb-3">
                        <label for="contactName" class="form-label">Ваше имя</label>
                        <input type="text" class="form-control" id="contactName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="contactPhone" class="form-label">Номер телефона</label>
                        <input type="text" class="form-control" id="contactPhone" name="phone" required placeholder="+998 XX XXX-XX-XX">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-primary" id="sendContactFormBtn">Отправить</button>
            </div>
        </div>
    </div>
</div>
