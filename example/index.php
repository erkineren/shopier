<?php

// example/index.php

use Shopier\Enums\ProductType;
use Shopier\Enums\WebsiteIndex;
use Shopier\Exceptions\NotRendererClassException;
use Shopier\Exceptions\RendererClassNotFoundException;
use Shopier\Exceptions\RequiredParameterException;
use Shopier\Models\Address;
use Shopier\Models\Buyer;
use Shopier\Renderers\AutoSubmitFormRenderer;
use Shopier\Renderers\IframeRenderer;
use Shopier\Renderers\ShopierButtonRenderer;
use Shopier\Shopier;

require_once __DIR__ . '/bootstrap.php';

$shopier = new Shopier(getenv('SHOPIER_API_KEY'), getenv('SHOPIER_API_SECRET'));

// Satın alan kişi bilgileri
$buyer = new Buyer([
    'id' => 101,
    'name' => 'Erkin',
    'surname' => 'Eren',
    'email' => 'eren@erkin.net',
    'phone' => '8503023601'
]);

// Fatura ve kargo adresi birlikte tanımlama
// Ayrı ayrı da tanımlanabilir
$address = new Address([
    'address' => 'Kızılay Mh.',
    'city' => 'Ankara',
    'country' => 'Turkey',
    'postcode' => '06100',
]);

// shopier parametrelerini al
$params = $shopier->getParams();

// Geri dönüş sitesini ayarla
$params->setWebsiteIndex(WebsiteIndex::SITE_1);

// Satın alan kişi bilgisini ekle
$params->setBuyer($buyer);

// Fatura ve kargo adresini aynı şekilde ekle
$params->setAddress($address);

// Sipariş numarası ve sipariş tutarını ekle
$params->setOrderData('52003', '1.0');

// Sipariş edilen ürünü ekle
$params->setProductData('Test Product', ProductType::DOWNLOADABLE_VIRTUAL);


try {

    /**
     * Shopier İle Güvenli Öde şeklinde butona tıklanınca ödeme sayfasına yönlendiren renderer
     */
    $renderer = new ShopierButtonRenderer($shopier);
    $renderer
        ->setName('Shopier ile Güvenli Öde');


    /**
     * Otomatik ödeme sayfasına yönlendiren renderer
     */
    $renderer = new AutoSubmitFormRenderer($shopier);


    /**
     * Otomatik ödeme sayfasına iframe olarak yönlendiren renderer
     */
    $renderer = new IframeRenderer($shopier);
    $renderer
        ->setWidth(600)
        ->setHeight(750)
        ->setCenter(true);


    $shopier->goWith($renderer);

} catch (RequiredParameterException $e) {
    // Zorunlu parametrelerden bir ve daha fazlası eksik
} catch (NotRendererClassException $e) {
    // $shopier->createRenderer(...) metodunda verilen class adı AbstractRenderer sınıfından türetilmemiş !
} catch (RendererClassNotFoundException $e) {
    // $shopier->createRenderer(...) metodunda verilen class bulunamadı !
}

