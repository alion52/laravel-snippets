<?php

namespace App\Services\Payments;

use App\Models\Payment;
use Illuminate\Http\Request;

/**
 * Обработка вебхуков от банка.
 */
class PaymentWebhookHandler
{
    public function handle(Request $request): void
    {
        // 1. Проверка подписи
        if (!$this->verifySignature($request)) {
            abort(403, 'Invalid signature');
        }

        // 2. Поиск платежа
        $payment = Payment::where('bank_order_id', $request->input('orderId'))->first();

        if (!$payment) {
            abort(404, 'Payment not found');
        }

        // 3. Проверка от повторной обработки
        if ($payment->isFinalized()) {
            return;
        }

        // 4. Обработка статуса
        match ((int) $request->input('status')) {
            1 => $payment->markAsPaid(),
            2 => $payment->markAsFailed(),
            3 => $payment->markAsRefunded(),
            default => $payment->markAsUnknown(),
        };

        // 5. Логирование
        logger()->info('Payment webhook processed', [
            'payment_id' => $payment->id,
            'status' => $request->input('status'),
        ]);
    }

    private function verifySignature(Request $request): bool
    {
        // Проверка подписи банка
        $expected = hash_hmac('sha256', $request->getContent(), config('services.alfabank.secret'));
        return hash_equals($expected, $request->header('X-Signature', ''));
    }
}
