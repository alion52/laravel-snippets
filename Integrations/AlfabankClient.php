<?php

namespace App\Services\Payments;

use Illuminate\Support\Facades\Http;

/**
 * Клиент для работы с эквайрингом Альфа-Банка.
 */
class AlfabankClient
{
    public function __construct(
        private string $username,
        private string $password,
        private string $gateway = 'https://pay.alfabank.ru/payment/rest/'
    ) {}

    /**
     * Регистрация платежа.
     */
    public function registerOrder(array $data): array
    {
        $response = Http::asForm()->post($this->gateway . 'register.do', array_merge([
            'userName' => $this->username,
            'password' => $this->password,
        ], $data));

        return $response->json();
    }

    /**
     * Получение статуса платежа.
     */
    public function getOrderStatus(string $orderId): array
    {
        $response = Http::asForm()->post($this->gateway . 'getOrderStatusExtended.do', [
            'userName' => $this->username,
            'password' => $this->password,
            'orderId' => $orderId,
        ]);

        return $response->json();
    }

    /**
     * Возврат средств.
     */
    public function refund(string $orderId, int $amount): array
    {
        $response = Http::asForm()->post($this->gateway . 'refund.do', [
            'userName' => $this->username,
            'password' => $this->password,
            'orderId' => $orderId,
            'amount' => $amount,
        ]);

        return $response->json();
    }
}
