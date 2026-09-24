<?php

namespace Shopier\Tests;

use Shopier\Exceptions\NotRendererClassException;
use Shopier\Exceptions\RendererClassNotFoundException;
use Shopier\Exceptions\RequiredParameterException;
use Shopier\Models\ShopierParams;
use Shopier\Renderers\AutoSubmitFormRenderer;
use Shopier\Shopier;

class ShopierTest extends TestCase
{
    public function testShopierInstanceCreation()
    {
        $shopier = $this->createShopier();

        $this->assertInstanceOf(Shopier::class, $shopier);
        $this->assertSame(self::API_KEY, $shopier->getApiKey());
        $this->assertSame(self::API_SECRET, $shopier->getApiSecret());
    }

    public function testGetParams()
    {
        $params = $this->createShopier()->getParams();

        $this->assertInstanceOf(ShopierParams::class, $params);
        $this->assertSame(self::API_KEY, $params->getAPIKey());
    }

    public function testConstructorUsesGivenParams()
    {
        $params = new ShopierParams();
        $shopier = new Shopier(self::API_KEY, self::API_SECRET, $params);

        $this->assertSame($params, $shopier->getParams());
        $this->assertSame(self::API_KEY, $params->getAPIKey());
    }

    public function testCalculateSignature()
    {
        $shopier = $this->createPreparedShopier();
        $params = $shopier->getParams();
        $params->setRandomNr(123456);

        $shopier->calculateSignature();

        $expected = base64_encode(hash_hmac('sha256', '12345610001' . '10.0' . '0', self::API_SECRET, true));
        $this->assertSame($expected, $params->getSignature());
    }

    public function testPrepareThrowsWhenRequiredParametersAreMissing()
    {
        $this->expectException(RequiredParameterException::class);

        $this->createShopier()->prepare();
    }

    public function testPrepareSucceedsWithAllRequiredParameters()
    {
        $shopier = $this->createPreparedShopier();

        $this->assertSame($shopier, $shopier->prepare());
        $this->assertNotEmpty($shopier->getParams()->getSignature());
    }

    public function testValidateResponse()
    {
        $shopier = $this->createShopier();
        $signature = base64_encode(hash_hmac('sha256', '123456' . '10001', self::API_SECRET, true));

        $response = [
            'platform_order_id' => '10001',
            'status' => 'success',
            'installment' => '0',
            'payment_id' => '954344654',
            'random_nr' => '123456',
            'signature' => $signature,
        ];

        $this->assertTrue($shopier->validateResponse($response));

        $response['signature'] = base64_encode('invalid');
        $this->assertFalse($shopier->validateResponse($response));
    }

    public function testCreateRenderer()
    {
        $renderer = $this->createShopier()->createRenderer(AutoSubmitFormRenderer::class);

        $this->assertInstanceOf(AutoSubmitFormRenderer::class, $renderer);
    }

    public function testCreateRendererThrowsForUnknownClass()
    {
        $this->expectException(RendererClassNotFoundException::class);

        $this->createShopier()->createRenderer('Shopier\\Renderers\\DoesNotExist');
    }

    public function testCreateRendererThrowsForNonRendererClass()
    {
        $this->expectException(NotRendererClassException::class);

        $this->createShopier()->createRenderer(\ArrayObject::class);
    }

    public function testGoWithReturnsRenderedOutput()
    {
        $shopier = $this->createPreparedShopier();
        $renderer = new AutoSubmitFormRenderer($shopier);

        $output = $shopier->goWith($renderer, true);

        $this->assertStringContainsString('<form id="shopier_payment_form"', $output);
        $this->assertStringContainsString('.submit();', $output);
    }

    public function testGoOutputsEscapedAutoSubmitPage()
    {
        $shopier = $this->createPreparedShopier();
        $shopier->getParams()->setProductName('"><script>alert(1)</script>');

        ob_start();
        $shopier->go();
        $output = ob_get_clean();

        $this->assertStringContainsString('<!DOCTYPE html>', $output);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $output);
        $this->assertStringContainsString('&quot;&gt;&lt;script&gt;', $output);
    }
}
