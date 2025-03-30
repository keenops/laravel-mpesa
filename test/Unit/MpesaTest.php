<?php

namespace Keenops\Mpesa\Tests;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Keenops\Mpesa\Mpesa;
use Orchestra\Testbench\TestCase;

class MpesaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Mock config values used in Mpesa class
        Config::set('laravel-mpesa', [
            'mpesa_api_key' => 'test-api-key',
            'mpesa_public_key' => file_get_contents(__DIR__.'/../Fixtures/public.key'), // should contain a valid test RSA public key
            'mpesa_market_country' => 'TZA',
            'mpesa_market_currency' => 'TZS',
            'mpesa_environment' => 'sandbox'
        ]);
    }

    public function test_c2b_payment_sends_request()
    {
        Http::fake([
            '*' => Http::response(['status' => 'Success'], 200),
        ]);

        $response = Mpesa::c2b('255700000000', '1000', 'SP123', 'REF001', 'CONV123', 'Test C2B Payment');

        $this->assertEquals('Success', $response['status']);
    }

    public function test_b2c_payment_sends_request()
    {
        Http::fake([
            '*' => Http::response(['status' => 'Success'], 200),
        ]);

        $response = Mpesa::b2c('255700000000', '2000', 'SP123', 'REF002', 'CONV124', 'Test B2C Payment');

        $this->assertEquals('Success', $response['status']);
    }

    public function test_b2b_payment_sends_request()
    {
        Http::fake([
            '*' => Http::response(['status' => 'Success'], 200),
        ]);

        $response = Mpesa::b2b('SP001', 'SP002', '3000', 'REF003', 'CONV125', 'Test B2B Payment');

        $this->assertEquals('Success', $response['status']);
    }

    public function test_transaction_reversal_sends_request()
    {
        Http::fake([
            '*' => Http::response(['status' => 'Reversed'], 200),
        ]);

        $response = Mpesa::reverse('1000', 'SP123', 'CONV126', 'TX12345');

        $this->assertEquals('Reversed', $response['status']);
    }

    public function test_transaction_status_query()
    {
        Http::fake([
            '*' => Http::response(['status' => 'Completed'], 200),
        ]);

        $response = Mpesa::transactionStatus('TX12345', 'SP123', 'CONV127');

        $this->assertEquals('Completed', $response['status']);
    }
}