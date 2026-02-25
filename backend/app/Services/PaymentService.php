<?php

namespace App\Services;

use Paymongo\PaymongoClient;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    protected $client;

    public function __construct()
    {
        $this->client = new PaymongoClient(config('services.paymongo.secret_key'));
    }

    public function createPaymentIntent(float $amount, string $description): string
    {
        try {
            // PayMongo amounts are in centavos (integer)
            $amountInCents = (int) ($amount * 100);

            // Create a Payment Intent
            $paymentIntent = $this->client->paymentIntent->create([
                'amount' => $amountInCents,
                'payment_method_allowed' => ['card'],
                'currency' => 'PHP',
                'description' => $description,
                'capture_type' => 'automatic',
            ]);

            return $paymentIntent->id;
        } catch (\Exception $e) {
            Log::error('PayMongo Payment Intent Creation Failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function processTestPayment(float $amount, string $description): array
    {
        try {
            $amountInCents = (int) ($amount * 100);

            // 1. Create Payment Method (Test Card)
            // Note: In production, this should come from the frontend (PayMongo.js)
            // But for this demo/test requirement, we use backend creation with test credentials.
            $paymentMethod = $this->client->paymentMethod->create([
                'type' => 'card',
                'details' => [
                    'card_number' => '4111111111111111', // Test Visa
                    'exp_month' => 12,
                    'exp_year' => 2030,
                    'cvc' => '123',
                ],
                'billing' => [
                    'address' => [
                        'line1' => 'Test Address',
                        'city' => 'Manila',
                        'postal_code' => '1000',
                        'country' => 'PH'
                    ],
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                    'phone' => '09123456789'
                ]
            ]);

            // 2. Create Payment Intent
            $paymentIntent = $this->client->paymentIntent->create([
                'amount' => $amountInCents,
                'payment_method_allowed' => ['card'],
                'currency' => 'PHP',
                'description' => $description,
                'capture_type' => 'automatic',
            ]);

            // 3. Attach Payment Method to Payment Intent
            $attachedIntent = $this->client->paymentIntent->attach($paymentIntent->id, [
                'payment_method' => $paymentMethod->id,
                'return_url' => route('dashboard'), // Placeholder
            ]);

            return [
                'id' => $attachedIntent->id,
                'status' => $attachedIntent->status,
            ];

        } catch (\Exception $e) {
            Log::error('PayMongo Test Payment Failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
