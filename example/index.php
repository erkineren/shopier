<?php

use Shopier\Exceptions\NotRendererClassException;
use Shopier\Exceptions\RendererClassNotFoundException;
use Shopier\Exceptions\RequiredParameterException;
use Shopier\Models\Address;
use Shopier\Models\Buyer;
use Shopier\Renderers\AutoSubmitFormRenderer;
use Shopier\Renderers\ButtonRenderer;
use Shopier\Enums\ProductType;
use Shopier\Shopier;

require 'vendor/autoload.php';

define('API_KEY', '***');
define('API_SECRET', '***');

$shopier = new Shopier(API_KEY, API_SECRET);

// Satın alan kişi bilgileri
$buyer = new Buyer([
    'id' => 101,
    'name' => 'Erkin',
    'surname' => 'Eren',
    'email' => 'eren@erkin.net',
    'phone' => '8503023601'
]);

// Fatura ve kargo adresi birlikte tanımlama
// Ayrı ayrı da tanımlabilir
$address = new Address([
    'address' => 'Kızılay Mh.',
    'city' => 'Ankara',
    'country' => 'Turkey',
    'postcode' => '06100',
]);

// shopier parametlerini al
$params = $shopier->getParams();

// Satın alan kişi bilgisini ekle
$params->setBuyer($buyer);

// Fatura ve kargo adresini aynı şekilde ekle
$params->setAddress($address);

// Sipariş numarsı ve sipariş tutarını ekle
$shopier->setOrderData('52003', '1.0');

// Sipariş edilen ürünü ekle
$shopier->setProductData('Test Product', ProductType::DOWNLOADABLE_VIRTUAL);

try {


    /**
     * Otomarik ödeme sayfasına yönlendiren renderer
     *
     * @var AutoSubmitFormRenderer $renderer
     */
//    $renderer = $shopier->createRenderer(AutoSubmitFormRenderer::class);


    /**
     * Shopier İle Güvenli Öde şeklinde butona tıklanınca ödeme sayfasına yönlendirenn renderer
     *
     * @var ButtonRenderer $renderer
     */
    $renderer = $shopier->createRenderer(ButtonRenderer::class);
    $renderer
        ->withStyle("padding:15px; color: #fff; background-color:#51cbb0; border:1px solid #fff; border-radius:7px")
        ->withText('Shopier İle Güvenli Öde');


    $shopier->goWith($renderer);

} catch (RequiredParameterException $e) {
    // Zorunlu parametlerden bir ve daha fazlası eksik
} catch (NotRendererClassException $e) {
    // $shopier->createRenderer(...) metodunda verilen class adı AbstracRenderer sınıfından türetilmemiş !
} catch (RendererClassNotFoundException $e) {
    // $shopier->createRenderer(...) metodunda verilen class bulunamadı !
}