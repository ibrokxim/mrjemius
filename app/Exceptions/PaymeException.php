<?php
// app/Exceptions/PaymeException.php
namespace App\Exceptions;

use Exception;
use Throwable;

class PaymeException extends Exception
{
    protected $paymeErrorCode;
    protected $paymeErrorData;
    protected $httpStatusCode; // Используется для логирования, не для ответа Payme
    protected $paymeMessageArray; // Для хранения мультиязычного сообщения

    public function __construct($message = "", int $paymeErrorCode = 0, $paymeErrorData = null, int $httpStatusCode = 400, Throwable $previous = null)
    {
        $this->paymeMessageArray = is_array($message) ? $message : ['ru' => (string)$message, 'uz' => (string)$message, 'en' => (string)$message];
        $resolvedMessage = $this->getMessageForPayme(); // Получаем сообщение для текущей локали для родительского конструктора

        parent::__construct($resolvedMessage, 0, $previous);
        $this->paymeErrorCode = $paymeErrorCode;
        $this->paymeErrorData = $paymeErrorData;
        $this->httpStatusCode = $httpStatusCode;
    }

    public function getPaymeErrorCode(): int
    {
        return $this->paymeErrorCode;
    }

    public function getPaymeErrorData()
    {
        return $this->paymeErrorData;
    }

    public function getHttpStatusCode(): int
    {
        return $this->httpStatusCode;
    }

    /**
     * Возвращает сообщение для Payme на основе текущей или fallback локали.
     * Payme может ожидать объект с локалями для сообщения.
     * @return array|string
     */
    public function getMessageForPayme()
    {
        // Согласно документации, message может быть объектом с ключами языков
        // "message": {"ru": "description_ru", "uz": "description_uz", "en":"description_en"}
        // Если у вас только одна строка, Payme может ее принять.
        // Если Payme требует объект, то возвращаем $this->paymeMessageArray
        // Если Payme принимает строку для текущей локали, то:
        // $currentLocale = app()->getLocale();
        // $fallbackLocale = config('app.fallback_locale');
        // return $this->paymeMessageArray[$currentLocale]
        //     ?? $this->paymeMessageArray[$fallbackLocale]
        //     ?? $this->paymeMessageArray['ru'] // Как запасной вариант
        //     ?? reset($this->paymeMessageArray) ?: "Unknown error";

        // Для простоты, если Payme поддерживает объект сообщений:
        return $this->paymeMessageArray;
    }
}
