<?php

namespace Keenops\Mpesa;

use Illuminate\Support\Facades\Http;
use phpseclib3\Crypt\PublicKeyLoader;

class Mpesa
{
    private $config;

    public function __construct() {
        $this->config = config('laravel-mpesa');
    }

    private function encryptKey($key): String {
        $pKey = PublicKeyLoader::load($this->config['mpesa_public_key']);
        openssl_public_encrypt($key, $encrypted, $pKey);
        return base64_encode($encrypted);
    }

    /**
     * Get session with the given encrypted key.
     *
     * @param string $encryptedKey
     * @return array
     */
    private function getSession($encryptedKey) {
        return $this->sendRequest('getSession/', 'get', ['token' => $encryptedKey]);
    }

    /**
     * Get the session token.
     *
     * @return string
     */
    private function getSessionToken() : String {
        $sessionKey = $this->getSession($this->encryptKey($this->config['mpesa_api_key']));
        return $sessionKey['output_SessionID'];
    }

    /**
     * Get the Mpesa market.
     *
     * @return string
     */
    private function mpesaMarket() : String {
        return ($this->config['mpesa_market_country'] === 'GHA' ? 'vodafone' : 'vodacom') . $this->config['mpesa_market_country'];
    }

    /**
     * Send a request to the Mpesa API.
     *
     * @param string $endpoint
     * @param string $method
     * @param array $data
     * @return array
     */
    private function sendRequest(string $endpoint, string $method, array $data = []): array {
        $url = "https://openapi.m-pesa.com/".$this->config['mpesa_environment']."/ipg/v2/".$this->mpesaMarket()."/".$endpoint;
        $response = Http::timeout(300)
            ->withToken($data['token'] ?? $this->encryptKey($this->getSessionToken()))
            ->withHeaders([
                'Origin' => '*',
                'Content-Type' => 'application/json'
            ])
            ->{$method}($url, $data);

        return json_decode($response->body(), true);
    }

    /**
     * Send a C2B payment.
     *
     * @param string $customerNumber
     * @param string $amount
     * @param string $serviceCode
     * @param string $reference
     * @param string $conversationId
     * @param string $description
     * @return array
     */
    public static function c2b(
        string $customerNumber,
        string $amount,
        string $serviceCode,
        string $reference,
        string $conversationId,
        string $description
    ) {
        $instance = new self();
        return $instance->sendRequest('c2bPayment/singleStage/', 'post', [
            'input_Amount' => $amount,
            'input_CustomerMSISDN' => $customerNumber,
            'input_Country' => $instance->config['mpesa_market_country'],
            'input_Currency' => $instance->config['mpesa_market_currency'],
            'input_ServiceProviderCode' => $serviceCode,
            'input_TransactionReference' => $reference,
            'input_ThirdPartyConversationID' => $conversationId,
            'input_PurchasedItemsDesc' => $description
        ]);
    }

    /**
     * Send a B2C payment.
     *
     * @param string $customerNumber
     * @param string $amount
     * @param string $serviceCode
     * @param string $reference
     * @param string $conversationId
     * @param string $description
     * @return array
     */
    public static function b2c(
        string $customerNumber,
        string $amount,
        string $serviceCode,
        string $reference,
        string $conversationId,
        string $description
    ) {
        $instance = new self();
        return $instance->sendRequest('b2cPayment/', 'post', [
            'input_Amount' => $amount,
            'input_CustomerMSISDN' => $customerNumber,
            'input_Country' => $instance->config['mpesa_market_country'],
            'input_Currency' => $instance->config['mpesa_market_currency'],
            'input_ServiceProviderCode' => $serviceCode,
            'input_TransactionReference' => $reference,
            'input_ThirdPartyConversationID' => $conversationId,
            'input_PaymentItemsDesc' => $description
        ]);
    }

    /**
     * Send a B2B payment.
     *
     * @param string $senderCode
     * @param string $receiverCode
     * @param string $amount
     * @param string $reference
     * @param string $conversationId
     * @param string $description
     * @return array
     */
    public static function b2b(
        string $senderCode,
        string $receiverCode,
        string $amount,
        string $reference,
        string $conversationId,
        string $description
    ) {
        $instance = new self();
        return $instance->sendRequest('b2bPayment/', 'post', [
            'input_Amount' => $amount,
            'input_Country' => $instance->config['mpesa_market_country'],
            'input_Currency' => $instance->config['mpesa_market_currency'],
            'input_PrimaryPartyCode' => $senderCode,
            'input_ReceiverPartyCode' => $receiverCode,
            'input_ThirdPartyConversationID' => $conversationId,
            'input_TransactionReference' => $reference,
            'input_PurchasedItemsDesc' => $description
        ]);
    }

    /**
     * Reverse a transaction.
     *
     * @param string $amount
     * @param string $serviceProviderCode
     * @param string $conversationId
     * @param string $transactionId
     * @return array
     */
    public static function reverse(
        string $amount,
        string $serviceProviderCode,
        string $conversationId,
        string $transactionId
    ) {
        $instance = new self();
        return $instance->sendRequest('reversal/', 'put', [
            'input_Country' => $instance->config['mpesa_market_country'],
            'input_ReversalAmount' => $amount,
            'input_ServiceProviderCode' => $serviceProviderCode,
            'input_ThirdPartyConversationID' => $conversationId,
            'input_TransactionID' => $transactionId
        ]);
    }

    /**
     * Get the status of a transaction.
     *
     * @param string $queryReference
     * @return array
     */
    public static function transactionStatus(
        string $queryReference,
        string $serviceProviderCode,
        string $conversationId,
    ) {
        $instance = new self();
        return $instance->sendRequest('queryTransactionStatus/', 'get', [
            'input_QueryReference' => $queryReference,
            'input_ServiceProviderCode' => $serviceProviderCode,
            'input_ThirdPartyConversationID' => $conversationId,
            'input_Country' => $instance->config['mpesa_market_country'],
        ]);
    }
}