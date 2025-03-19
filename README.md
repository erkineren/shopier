# Shopier PHP API SDK

[![Latest Version on Packagist](https://img.shields.io/packagist/v/erkineren/shopier.svg?style=flat-square)](https://packagist.org/packages/erkineren/shopier)
[![Total Downloads](https://img.shields.io/packagist/dt/erkineren/shopier.svg?style=flat-square)](https://packagist.org/packages/erkineren/shopier)
[![License](https://img.shields.io/packagist/l/erkineren/shopier.svg?style=flat-square)](https://packagist.org/packages/erkineren/shopier)

A PHP SDK for Shopier Payment Gateway integration. This package allows you to easily integrate Shopier payment services into your PHP applications.

_Türkçe açıklama için aşağıya bakınız._

![shopier-api](https://user-images.githubusercontent.com/16518847/56689086-e90b8880-66e2-11e9-92a6-45dccfd410db.png)

## Installation

You can install the package via composer:

```bash
composer require erkineren/shopier
```

## Usage

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
use Shopier\Renderers\IframeRenderer;
use Shopier\Renderers\ShopierButtonRenderer;
use Shopier\Shopier;

require_once __DIR__ . '/bootstrap.php';

$shopier = new Shopier($_ENV['SHOPIER_API_KEY'], $_ENV['SHOPIER_API_SECRET']);

// Buyer information
$buyer = new Buyer([
    'id' => 101,
    'name' => 'John',
    'surname' => 'Doe',
    'email' => 'example@example.com',
    'phone' => '5551234567'
]);

// Billing and shipping address
$address = new Address([
    'address' => '123 Main St',
    'city' => 'Istanbul',
    'country' => 'Turkey',
    'postcode' => '34000',
]);

// Get shopier parameters
$params = $shopier->getParams();

// Set return website index
$params->setWebsiteIndex(WebsiteIndex::SITE_1);

// Add buyer information
$params->setBuyer($buyer);

// Add address information
$params->setAddress($address);

// Set order number and amount
$params->setOrderData('52003', '10.0');

// Add product information
$params->setProductData('Test Product', ProductType::DOWNLOADABLE_VIRTUAL);


try {

    /**
     * ShopierButtonRenderer - Redirects to the payment page after clicking the button
     */
    $renderer = new ShopierButtonRenderer($shopier);
    $renderer->setName('Pay Securely with Shopier');


    /**
     * AutoSubmitFormRenderer - Automatically redirects to the payment page
     */
    //$renderer = new AutoSubmitFormRenderer($shopier);


    /**
     * IframeRenderer - Displays the payment page in an iframe
     */
    //$renderer = new IframeRenderer($shopier);
    //$renderer
    //    ->setWidth(600)
    //    ->setHeight(750)
    //    ->setCenter(true);


    $shopier->goWith($renderer);

} catch (RequiredParameterException $e) {
    // One or more required parameters are missing
    echo $e->getMessage();
} catch (NotRendererClassException $e) {
    // The class provided in $shopier->createRenderer(...) is not derived from AbstractRenderer
    echo $e->getMessage();
} catch (RendererClassNotFoundException $e) {
    // The class provided in $shopier->createRenderer(...) was not found
    echo $e->getMessage();
}
```

## Renderers

The library includes 3 different renderers:

1. **ShopierButtonRenderer**: Redirects to payment page after button click. Uses Shopier styled button.
2. **AutoSubmitFormRenderer**: Directly redirects to the payment page.
3. **IframeRenderer**: Embeds the payment page in an iframe (for pop-up methods without leaving the site).

### ShopierButtonRenderer

```php
use Shopier\Renderers\ShopierButtonRenderer;

$renderer = new ShopierButtonRenderer($shopier);
$renderer->setName('Pay Securely with Shopier');

$shopier->goWith($renderer);
```

![shopier-api-button](https://user-images.githubusercontent.com/16518847/56689087-e9a41f00-66e2-11e9-9d92-a602088ab933.png)

### AutoSubmitFormRenderer

```php
use Shopier\Renderers\AutoSubmitFormRenderer;

$renderer = new AutoSubmitFormRenderer($shopier);

$shopier->goWith($renderer);
```

### IframeRenderer

```php
use Shopier\Renderers\IframeRenderer;

$renderer = new IframeRenderer($shopier);
$renderer
    ->setWidth(600)
    ->setHeight(750)
    ->setCenter(true);

$shopier->goWith($renderer);
```

## Custom Renderers

You can create your own renderers by extending the `AbstractRenderer` class and implementing the `render` method, or you can use the `ButtonRenderer` class to design your own buttons.

```php
use Shopier\Renderers\ButtonRenderer;

$renderer = $shopier->createRenderer(ButtonRenderer::class);
$renderer
    ->withStyle("padding:15px; color: #fff; background-color:#51cbb0; border:1px solid #fff; border-radius:7px")
    ->withText('Pay Securely with Shopier');

$shopier->goWith($renderer);
```

## Verifying Payment Response

You can verify the payment response in your callback/return page as shown below:

```php
<?php
// example/return_url_page.php

use Shopier\Models\ShopierResponse;

require_once __DIR__ . '/bootstrap.php';

$shopierResponse = ShopierResponse::fromPostData();

if (!$shopierResponse->hasValidSignature(getenv('SHOPIER_API_SECRET'))) {
    // Payment failed
    die('Payment failed');
}

/*
 * Payment was successful
 * Process post-payment operations
 */
print_r($shopierResponse->toArray());
```

Response data structure:

```
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

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## Security

If you discover any security-related issues, please email hello@erkin.net instead of using the issue tracker.

## Credits

- [Erkin Eren](https://github.com/erkineren)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

---

# Shopier Api Sdk (Türkçe)

Yazdığınız özel yazılımlarınıza **Shopier ile Ödeme Alma** metodu ekleyebilirsiniz.

Shopier Api Entegrasyonu ile çok kolay bir şekilde entegrasyon sağlayın.

## Kurulum

```bash
composer require erkineren/shopier
```

## Kullanım

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
use Shopier\Renderers\IframeRenderer;
use Shopier\Renderers\ShopierButtonRenderer;
use Shopier\Shopier;

require_once __DIR__ . '/bootstrap.php';

$shopier = new Shopier($_ENV['SHOPIER_API_KEY'], $_ENV['SHOPIER_API_SECRET']);

// Satın alan kişi bilgileri
$buyer = new Buyer([
    'id' => 101,
    'name' => 'Erkin',
    'surname' => 'Eren',
    'email' => 'hello@erkin.net',
    'phone' => '8503023601'
]);

// Fatura ve kargo adresi birlikte tanımlama
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
$params->setOrderData('52003', '10.0');

// Sipariş edilen ürünü ekle
$params->setProductData('Test Ürün', ProductType::DOWNLOADABLE_VIRTUAL);


try {
    $renderer = new ShopierButtonRenderer($shopier);
    $renderer->setName('Shopier ile Güvenli Öde');

    $shopier->goWith($renderer);

} catch (RequiredParameterException $e) {
    echo $e->getMessage();
} catch (NotRendererClassException $e) {
    echo $e->getMessage();
} catch (RendererClassNotFoundException $e) {
    echo $e->getMessage();
}
```

Daha fazla bilgi için İngilizce dokümantasyona bakınız.
