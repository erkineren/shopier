<?php

use Shopier\Models\ShopierResponse;

require_once __DIR__ . '/bootstrap.php';

// $_POST içerisinde aşağıdaki şekilde veriler gelir
//[
//    'platform_order_id' => '10002',
//    'API_key' => '*****',
//    'status' => 'success',
//    'installment' => '0',
//    'payment_id' => '954344654',
//    'random_nr' => '123456',
//    'signature' => 'f3EjDlXoPICsKssHT9iv/5ddCXIwk1ZcItlYXDqyYHrNso=',
//];

$shopierResponse = ShopierResponse::fromPostData();

if (!$shopierResponse->hasValidSignature(getenv('SHOPIER_API_SECRET'))) {
    //TODO: Ödeme başarılı değil, hata mesajı göster
    die('Ödemeniz alınamadı');
}

/*
 *
 * TODO: Ödeme başarıyla gerçekleşti. Ödeme sonrası işlemleri uygula
 *
 */
print_r($shopierResponse->toArray());