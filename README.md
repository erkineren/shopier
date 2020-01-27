# Shopier Api Sdk

Yazdığınız özel yazılımlarınıza **Shopier ile Ödeme Alma** metodu ekleyebilirsiniz.

Shopier Api Entegrasyonu ile çok kolay bir şekilde entegrasyon sağlayın.


![shopier-api](https://user-images.githubusercontent.com/16518847/56689086-e90b8880-66e2-11e9-92a6-45dccfd410db.png)



# Installation (Kurulum)

```
composer require erkineren/shopier
```

# Usage (Kullanım)

```php
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


```

# Renderer (Ödeme sayfasına yönlendirme yöntemleri)

Kütüphane içerisinde 2 adet Renderer vardır.
- ButtonRenderer: Butona tıklanınca ödeme sayfasına gider.
- AutoSubmitFormRenderer: Direk ödeme sayfasına gider.


![shopier-api-ozel-buton](https://user-images.githubusercontent.com/16518847/56689087-e9a41f00-66e2-11e9-9d92-a602088ab933.png)

```php
$renderer = $shopier->createRenderer(ButtonRenderer::class);
$renderer
    ->withStyle("padding:15px; color: #fff; background-color:#51cbb0; border:1px solid #fff; border-radius:7px")
    ->withText('Shopier İle Güvenli Öde');

$shopier->goWith($renderer);
```


- AutoSubmitFormRenderer: Sayfa açıldığı gibi ödeme sayfasına gider.
```php
$renderer = $shopier->createRenderer(AutoSubmitFormRenderer::class);

$shopier->goWith($renderer);
```

# Custom Renderer (Özel Yönlendirme Şekli Ekleme)
- Kendi rendererlarınızı oluşturmak için ``AbstractRenderer`` sınıfından yeni bir sınıf türeterek ``render`` metodu 
içerisine kendi yönlendirme uygulamanızı yazabilirsiniz.


# Verify Payment Response (Ödeme Sayfasından Dönen Verileri Kontrol Etme)
Ödeme sonrası dönüş url'nizdeki sayfa içerisinde (callback/return page) aşağıdaki gösterildiği gibi kontrol yapabilirsiniz.

```php
<?php
// example/return_url_page.php

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
```

```
// $response_data dizisi aşağıdaki şekildedir
// Ödeme sonrasında Shopier tarafından sizin return_url'nize post edilen veridir.
Array
(
    [platform_order_id] => 20002
    [API_key] => *******************************
    [status] => success
    [installment] => 0
    [payment_id] => 446549593
    [random_nr] => 528061
    [signature] => +e1klzFG7ZABS16xnHcZ8peqbvSZD3Pv9NU4pWiw0qE=
)
```

# Parameter Initialize Methods (Parametre Ekleme Yöntemleri)
``ShopierParams``, ``Buyer`` ve ``Address`` sınıfları ``BaseModel`` sınıfında türemektedir.
``ShippingAddress`` ve ``BillingAddress`` sınıfları ``Address`` sınıfından türemektedir.

```php
$params->setBuyer(Buyer::fromArray([...]));

$params->setBillingAddress(new Address([...]));

$params->setShippingAddress(new ShippingAddress([...]));
```

BaseModel içerisinde kullanılabilecek kullanışlı metotlar vardır:

```php

public function __construct(array $values = [])

public static function fromArray(array $properties)

public function toArray()
 
public function toJson()
```

# Enums
Kütüphane içerisinde 4 adet enum sınıf bulunmaktadır.

- Curreny
```php
Currency::TL
Currency::USD
Currency::EUR
```
- Language
```php
Language::TR
Language::EN
```
- ProductType
```php
ProductType::REAL
ProductType::DOWNLOADABLE_VIRTUAL
ProductType::DEFAULT_TYPE
```
- WebsiteIndex
```php
WebsiteIndex::SITE_1
WebsiteIndex::SITE_2
WebsiteIndex::SITE_3
WebsiteIndex::SITE_4
WebsiteIndex::SITE_5
```

# Exceptions
- NotRendererClassException: ``createRender()`` metodu içerisinde gönderilen sınıf AbstractRenderer sınıfından türememiş
- RendererClassNotFoundException: `createRender()`` metodu içerisinde gönderilen sınıf bulunamadı
- RequiredParameterException: ``ShopierParams`` sınıfındaki zorunlu propertylerden  bir veya birkaçı boş

# Support (Destek)

Entegrasyon talepleriniz için [tıklayınız](https://wa.me/908503023601?text=Shopier+entegrasyonu+yapt%C4%B1rmak+istiyorum).

Email: [hello@erkin.net](mailto:hello@erkin.net)
