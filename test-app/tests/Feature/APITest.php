<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Набор тестов, проверяющих сервис DivergeChecker в окружении всего приложения.
 */
class APITest extends TestCase
{
    /**
     * Корректный запрос.
     *
     * @return void
     */
    public function test_the_api_returns_a_successful_response(): void
    {
        $response = $this->json('POST', '/api/v1/checks', [
            'price' => [
                'threshold' => 50,
                'new' => 105,
                'out' => 200,
            ],
        ]);

        $response
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJson([
                'price' => [
                    'threshold' => 50,
                    'new' => 105,
                    'out' => 200,
                ],
            ]);
    }

    public function test_the_api_returns_a_successful_response_whit_default_threshold()
    {
        $response = $this->json('POST', '/api/v1/checks', [
            'price' => [
                'new' => 105,
                'out' => 200,
            ],
        ]);

        $response
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJson([
                'price' => [
                    'new' => 105,
                    'out' => 200,
                ],
            ]);
    }

    public function test_the_not_valid_threshold()
    {
        $response = $this->json('POST', '/api/v1/checks', [
            'price' => [
                'threshold' => '5k0',
                'new' => 50.14,
                'out' => 2000.99,
            ],
        ]);

        $response
            ->assertStatus(422)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJson([
                'message' => 'Not valid threshold.',
                'errors' => ['price' => true],
            ]);
    }

    public function test_the_negative_threshold()
    {
        $response = $this->json('POST', '/api/v1/checks', [
            'price' => [
                'threshold' => -5,
                'new' => 50.14,
                'out' => 2000.99,
            ],
        ]);

        $response
            ->assertStatus(422)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJson([
                'message' => 'Negative or zero threshold.',
                'errors' => ['price' => true],
            ]);
    }

    public function test_the_not_valid_new_price()
    {
        $response = $this->json('POST', '/api/v1/checks', [
            'price' => [
                'new' => '50r.14',
                'out' => 2000.99,
            ],
        ]);

        $response
            ->assertStatus(422)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJson([
                'message' => 'Invalid or absent new price.',
                'errors' => ['price' => true],
            ]);
    }

    public function test_the_negative_new_price()
    {
        $response = $this->json('POST', '/api/v1/checks', [
            'price' => [
                'new' => '-50.14',
                'out' => 2000.99,
            ],
        ]);

        $response
            ->assertStatus(422)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJson([
                'message' => 'Negative or zero new price.',
                'errors' => ['price' => true],
            ]);
    }

    public function test_the_not_valid_out_price()
    {
        $response = $this->json('POST', '/api/v1/checks', [
            'price' => [
                'new' => 50.14,
                'out' => '2d000.99',
            ],
        ]);

        $response
            ->assertStatus(422)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJson([
                'message' => 'Invalid or absent out price.',
                'errors' => ['price' => true],
            ]);
    }

    public function test_the_negative_out_price()
    {
        $response = $this->json('POST', '/api/v1/checks', [
            'price' => [
                'new' => 50.14,
                'out' => -2000.99,
            ],
        ]);

        $response
            ->assertStatus(422)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJson([
                'message' => 'Negative or zero out price.',
                'errors' => ['price' => true],
            ]);
    }

    public function test_the_new_price_is_over_threshold()
    {
        $response = $this->json('POST', '/api/v1/checks', [
            'price' => [
                'threshold' => 10,
                'new' => 500,
                'out' => 200,
            ],
        ]);

        $response
            ->assertStatus(422)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJson([
                'message' => 'Price change is very significant. Amount: 150%',
                'errors' => ['price' => true],
            ]);
    }

    public function test_the_new_price_is_lower_threshold()
    {
        $response = $this->json('POST', '/api/v1/checks', [
            'price' => [
                'threshold' => 10,
                'new' => 5,
                'out' => 200,
            ],
        ]);

        $response
            ->assertStatus(422)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJson([
                'message' => 'Price change is very significant. Amount: -97.5%',
                'errors' => ['price' => true],
            ]);
    }

    public function test_the_new_price_is_over_default_threshold()
    {
        $response = $this->json('POST', '/api/v1/checks', [
            'price' => [
                'new' => 5000.12,
                'out' => 200.11,
            ],
        ]);

        $response
            ->assertStatus(422)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJson([
                'message' => 'Price change is very significant. Amount: 2398.6857228524%',
                'errors' => ['price' => true],
            ]);
    }

    public function test_the_new_price_is_lower_default_threshold()
    {
        $response = $this->json('POST', '/api/v1/checks', [
            'price' => [
                'new' => 50.14,
                'out' => 2000.99,
            ],
        ]);

        $response
            ->assertStatus(422)
            ->assertHeader('Content-Type', 'application/json')
            ->assertJson([
                'message' => 'Price change is very significant. Amount: -97.494240351026%',
                'errors' => ['price' => true],
            ]);
    }
}
