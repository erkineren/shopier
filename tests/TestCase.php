<?php

namespace Shopier\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Shopier\Enums\ProductType;
use Shopier\Models\Address;
use Shopier\Models\Buyer;
use Shopier\Shopier;

abstract class TestCase extends BaseTestCase
{
    protected const API_KEY = 'test_api_key';
    protected const API_SECRET = 'test_api_secret';

    protected function createShopier()
    {
        return new Shopier(self::API_KEY, self::API_SECRET);
    }

    protected function createPreparedShopier()
    {
        $shopier = $this->createShopier();

        $shopier->getParams()
            ->setBuyer(new Buyer([
                'id' => 101,
                'name' => 'John',
                'surname' => 'Doe',
                'email' => 'john@example.com',
                'phone' => '5551234567',
            ]))
            ->setAddress(new Address([
                'address' => '123 Main St',
                'city' => 'Istanbul',
                'country' => 'Turkey',
                'postcode' => '34000',
            ]))
            ->setOrderData('10001', '10.0')
            ->setProductData('Test Product', ProductType::DOWNLOADABLE_VIRTUAL);

        return $shopier;
    }
}
