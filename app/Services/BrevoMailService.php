<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class BrevoMailService
{
    public static function send($toEmail, $subject, $htmlContent)
    {
        $apiKey = config('services.brevo.key');

        $data = [
            'sender' => [
                'name'  => config('mail.from.name'),
                'email' => config('mail.from.address'),
            ],
            'to' => [
                [
                    'email' => $toEmail,
                ]
            ],
            'subject'     => $subject,
            'htmlContent' => $htmlContent,
        ];

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL            => 'https://api.brevo.com/v3/smtp/email',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($data),
            CURLOPT_HTTPHEADER     => [
                'accept: application/json',
                'content-type: application/json',
                'api-key: ' . $apiKey,
            ],
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            Log::error('Erro cURL Brevo', [
                'error' => curl_error($ch),
            ]);
        }

        curl_close($ch);

        return $response;
    }
}
