<?php

use Shopier\Enums\ProductType;
use Shopier\Enums\WebsiteIndex;
use Shopier\Exceptions\NotRendererClassException;
use Shopier\Exceptions\RendererClassNotFoundException;
use Shopier\Exceptions\RequiredParameterException;
use Shopier\Models\Address;
use Shopier\Models\Buyer;
use Shopier\Renderers\AutoSubmitFormRenderer;
use Shopier\Renderers\ButtonRenderer;
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
     * Otomatik ödeme sayfasına yönlendiren renderer
     *
     * @var AutoSubmitFormRenderer $renderer
     */
//    $renderer = $shopier->createRenderer(AutoSubmitFormRenderer::class);
//    $shopier->goWith($renderer);

    /**
     * Shopier İle Güvenli Öde şeklinde butona tıklanınca ödeme sayfasına yönlendiren renderer
     *
     * @var ButtonRenderer $renderer
     */
    $renderer = $shopier->createRenderer(ButtonRenderer::class);
    $renderer
        ->withStyle("padding:15px; color: #fff; background-color:#51cbb0; border:1px solid #fff; border-radius:7px")
        ->withText('Shopier İle Güvenli Öde');


    $shopier->goWith($renderer);

} catch (RequiredParameterException $e) {
    // Zorunlu parametrelerden bir ve daha fazlası eksik
} catch (NotRendererClassException $e) {
    // $shopier->createRenderer(...) metodunda verilen class adı AbstractRenderer sınıfından türetilmemiş !
} catch (RendererClassNotFoundException $e) {
    // $shopier->createRenderer(...) metodunda verilen class bulunamadı !
}

