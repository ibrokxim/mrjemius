<div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="contactModalLabel">{{__('support')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>{{__('give contacts')}}</p>
                {{-- Форма будет отправляться через AJAX --}}
                <form id="contact-form">
                    <div class="mb-3">
                        <label for="contactName" class="form-label">{{__('your name')}}</label>
                        <input type="text" class="form-control" id="contactName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="contactPhone" class="form-label">{{__('your phone')}}</label>
                        <input type="text" class="form-control" id="contactPhone" name="phone" required placeholder="+998 XX XXX-XX-XX">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('close')}}</button>
                <button type="button" class="btn btn-primary" id="sendContactFormBtn">{{__('send')}}</button>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const phoneInput = document.getElementById('contactPhone');

        if (phoneInput) {
            // Определяем опции для маски
            const phoneMaskOptions = {
                mask: '+{998} (00) 000-00-00',
                lazy: false, // Маска будет видна сразу
            };

            // Применяем маску к элементу
            const mask = IMask(phoneInput, phoneMaskOptions);
        }

        // ... ваш другой JS-код ...
    });
</script>
