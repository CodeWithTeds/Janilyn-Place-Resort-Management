<?php

namespace App\Services;

use Paymongo\PaymongoClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class PaymentService
{
    protected $client;
    protected $secretKey;

    public function __construct()
    {
        $this->secretKey = config('services.paymongo.secret_key');
        $this->client = new PaymongoClient($this->secretKey);
    }

    public function createCheckoutSession(array $data): array
    {
        try {
            // Using HTTP Client because the SDK might not fully support Checkout Sessions
            $response = Http::withBasicAuth($this->secretKey, '')
                ->post('https://api.paymongo.com/v1/checkout_sessions', [
                    'data' => [
                        'attributes' => [
                            'billing' => [
                                'name' => $data['name'],
                                'email' => $data['email'],
                                'phone' => $data['phone'],
                            ],
                            'line_items' => [
                                [
                                    'currency' => 'PHP',
                                    'amount' => (int) ($data['amount'] * 100),
                                    'description' => $data['description'],
                                    'name' => $data['description'], // Using description as name for simplicity
                                    'quantity' => 1,
                                ]
                            ],
                            'payment_method_types' => ['card', 'gcash', 'paymaya', 'grab_pay', 'dob'],
                            'reference_number' => (string) $data['reference_number'],
                            'send_email_receipt' => false,
                            'show_description' => true,
                            'show_line_items' => true,
                            'cancel_url' => $data['cancel_url'],
                            'success_url' => $data['success_url'],
                            'description' => $data['description'],
                        ]
                    ]
                ]);

            if ($response->failed()) {
                throw new \Exception('PayMongo API Error: ' . $response->body());
            }

            $body = $response->json();
            
            return [
                'checkout_url' => $body['data']['attributes']['checkout_url'],
                'id' => $body['data']['id'],
            ];

        } catch (\Exception $e) {
            Log::error('PayMongo Checkout Session Creation Failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getCheckoutSession(string $sessionId): array
    {
        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->get("https://api.paymongo.com/v1/checkout_sessions/{$sessionId}");

            if ($response->failed()) {
                throw new \Exception('PayMongo API Error: ' . $response->body());
            }

            $body = $response->json();
            
            return [
                'id' => $body['data']['id'],
                'status' => $body['data']['attributes']['payment_intent']['attributes']['status'] ?? 'unpaid',
                'payments' => $body['data']['attributes']['payments'] ?? [],
            ];
        } catch (\Exception $e) {
            Log::error('PayMongo Get Checkout Session Failed: ' . $e->getMessage());
            throw $e;
        }
    }
}

