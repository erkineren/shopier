<?php

namespace Shopier\Tests;

use Shopier\Models\ShopierResponse;

class ShopierResponseTest extends TestCase
{
    private function responseData(array $overrides = [])
    {
        return array_merge([
            'platform_order_id' => '10002',
            'API_key' => self::API_KEY,
            'status' => 'success',
            'installment' => '0',
            'payment_id' => '954344654',
            'random_nr' => '123456',
            'signature' => base64_encode(hash_hmac('sha256', '123456' . '10002', self::API_SECRET, true)),
        ], $overrides);
    }

    public function testGetters()
    {
        $response = ShopierResponse::fromArray($this->responseData());

        $this->assertSame('10002', $response->getPlatformOrderId());
        $this->assertSame(self::API_KEY, $response->getAPIKey());
        $this->assertSame('success', $response->getStatus());
        $this->assertTrue($response->isSuccess());
        $this->assertSame('0', $response->getInstallment());
        $this->assertSame('954344654', $response->getPaymentId());
        $this->assertSame('123456', $response->getRandomNr());
    }

    public function testValidSignature()
    {
        $response = ShopierResponse::fromArray($this->responseData());

        $this->assertTrue($response->hasValidSignature(self::API_SECRET));
    }

    public function testInvalidSignatureWithWrongSecret()
    {
        $response = ShopierResponse::fromArray($this->responseData());

        $this->assertFalse($response->hasValidSignature('wrong_secret'));
    }

    public function testInvalidSignatureWithTamperedOrderId()
    {
        $response = ShopierResponse::fromArray($this->responseData(['platform_order_id' => '99999']));

        $this->assertFalse($response->hasValidSignature(self::API_SECRET));
    }

    public function testInvalidSignatureWhenRequiredFieldsAreMissing()
    {
        $data = $this->responseData();
        unset($data['payment_id']);

        $this->assertFalse(ShopierResponse::fromArray($data)->hasValidSignature(self::API_SECRET));
        $this->assertFalse(ShopierResponse::fromArray([])->hasValidSignature(self::API_SECRET));
    }

    public function testFromPostData()
    {
        $_POST = $this->responseData(['status' => 'failed']);

        $response = ShopierResponse::fromPostData();

        $this->assertFalse($response->isSuccess());
        $this->assertTrue($response->hasValidSignature(self::API_SECRET));

        $_POST = [];
    }
}
