<?php

namespace Keenops\Mpesa;

use Illuminate\Support\Facades\Http;
use phpseclib3\Crypt\PublicKeyLoader;


class Mpesa
{
    private $config;

    // Constructor to load all configuration values at once
    public function __construct() {
        $this->config = config('laravel-mpesa');
    }

    // Method to encrypt the API key using a public key
    private function encryptKey($key): String {
        $pKey = PublicKeyLoader::load($this->config['mpesa_public_key']);
        openssl_public_encrypt($key, $encrypted, $pKey);
        return base64_encode($encrypted);
    }

    // Method to get a session ID from the M-Pesa API
    private function getSession($encryptedKey) {
        $response = Http::timeout(9000)
            ->withToken($encryptedKey)
            ->withHeaders([
                'Origin' => '*',
                'Content-Type' => 'application/json'
            ])
            ->get("https://openapi.m-pesa.com/".$this->config['mpesa_environment']."/ipg/v2/vodacom".$this->config['mpesa_market_country']."/getSession/");

        return json_decode($response->body(), true);
    }

    // Method to get a session token from the M-Pesa API
    private function getSessionToken() : String {
        $sessionKey = $this->getSession($this->encryptKey($this->config['mpesa_api_key']));
        return $sessionKey['output_SessionID'];
    }

    // Method to make a customer-to-business (C2B) payment via the M-Pesa API
    public static function c2b(
        $customerNumber,
        $amount,
        $serviceCode,
        $reference,
        $conversationId,
        $description
    ) {
        // Create a new instance of the Mpesa class
        $instance = new self();

        // Encrypt the session token
        $token = $instance->encryptKey($instance->getSessionToken());

        // Make a POST request to the M-Pesa API
        $response = Http::timeout(300)
            ->withToken($token) // Set the encrypted session token as the authorization header
            ->withHeaders([
                'Origin' => '*', // Set the origin header
                'Content-Type' => 'application/json' // Set the content type header
            ])
            // Set the URL of the API endpoint
            ->post("https://openapi.m-pesa.com/".$instance->config['mpesa_environment']."/ipg/v2/vodacom".$instance->config['mpesa_market_country']."/c2bPayment/singleStage/",
            [
                // Set the body of the request
                'input_Amount' => $amount,
                'input_CustomerMSISDN' => $customerNumber,
                'input_Country' => $instance->config['mpesa_market_country'],
                'input_Currency' => $instance->config['mpesa_market_currency'],
                'input_ServiceProviderCode' => $serviceCode,
                'input_TransactionReference' => $reference,
                'input_ThirdPartyConversationID' => $conversationId,
                'input_PurchasedItemsDesc' => $description
            ]
        );

        // Return the response from the M-Pesa API as a JSON object
        return json_decode($response->body(), true);
    }
}
