<?php

namespace Shopier\Tests;

use PHPUnit\Framework\TestCase;
use Shopier\Shopier;

/**
 * @requires extension json
 */
class ShopierTest extends TestCase
{
    private $apiKey = 'test_api_key';
    private $apiSecret = 'test_api_secret';

    /**
     * @test
     */
    public function testShopierInstanceCreation()
    {
        $shopier = new Shopier($this->apiKey, $this->apiSecret);
        $this->assertInstanceOf(Shopier::class, $shopier);
    }

    /**
     * @test
     */
    public function testGetParams()
    {
        $shopier = new Shopier($this->apiKey, $this->apiSecret);
        $params = $shopier->getParams();
        $this->assertNotNull($params);
    }
}
