<?php

namespace Tests\Unit;

use Tests\TestCase;
use Keenops\Mpesa\Mpesa;
use Illuminate\Support\Facades\Http;

class MpesaTest extends TestCase
{
    public function testC2b()
    {
        // Mock the HTTP facade
        Http::fake([
            'https://openapi.m-pesa.com/*' => Http::response(['output_SessionID' => 'test_session_id', 'output_ResponseCode' => '0', 'output_ResponseDesc' => 'success'], 200),
        ]);

        // Call the c2b method
        $response = Mpesa::c2b(
            '254700000000', // Test customer number
            '100', // Test amount
            '000000', // Test service code
            'test_reference', // Test transaction reference
            'test_conversation_id', // Test conversation ID
            'test_description' // Test description
        );

        // Assert that the response is as expected
        $this->assertEquals('0', $response['output_ResponseCode']);
        $this->assertEquals('success', $response['output_ResponseDesc']);
    }
}