<?php

namespace Shopier\Tests;

use Shopier\Renderers\AutoSubmitFormRenderer;
use Shopier\Renderers\ButtonRenderer;
use Shopier\Renderers\FormRenderer;
use Shopier\Renderers\IframeRenderer;
use Shopier\Renderers\ShopierButtonRenderer;

class RendererTest extends TestCase
{
    public function testFormRendererContainsAllParams()
    {
        $shopier = $this->createPreparedShopier();
        $renderer = new FormRenderer($shopier);
        $renderer->render();
        $output = $renderer->output(true);

        $this->assertStringContainsString('action="https://www.shopier.com/ShowProduct/api_pay4.php"', $output);
        foreach (array_keys($shopier->getParams()->toArray()) as $key) {
            $this->assertStringContainsString('name="' . $key . '"', $output);
        }
        $this->assertStringContainsString(
            'name="signature" value="' . htmlspecialchars($shopier->getParams()->getSignature()) . '"',
            $output
        );
    }

    public function testFormRendererEscapesValues()
    {
        $shopier = $this->createPreparedShopier();
        $shopier->getParams()->setProductName('Product "X" <b>&</b>');

        $renderer = new FormRenderer($shopier);
        $renderer->render();
        $output = $renderer->output(true);

        $this->assertStringContainsString(
            'value="Product &quot;X&quot; &lt;b&gt;&amp;&lt;/b&gt;"',
            $output
        );
    }

    public function testOutputEchoesUnlessReturnRequested()
    {
        $renderer = new FormRenderer($this->createPreparedShopier());
        $renderer->render();

        ob_start();
        $returned = $renderer->output();
        $echoed = ob_get_clean();

        $this->assertSame($returned, $echoed);

        ob_start();
        $renderer->output(true);
        $this->assertSame('', ob_get_clean());
    }

    public function testAutoSubmitFormRenderer()
    {
        $renderer = new AutoSubmitFormRenderer($this->createPreparedShopier());
        $renderer->render();

        $this->assertStringContainsString(
            'document.getElementById("shopier_payment_form").submit();',
            $renderer->output(true)
        );
    }

    public function testButtonRendererEscapesAttributes()
    {
        $renderer = new ButtonRenderer($this->createPreparedShopier());
        $renderer
            ->withId('pay')
            ->withClass('btn "primary"')
            ->withText('Pay');
        $renderer->render();
        $output = $renderer->output(true);

        $this->assertStringContainsString(
            '<button type="submit" id="pay" class="btn &quot;primary&quot;">Pay</button>',
            $output
        );
    }

    public function testShopierButtonRendererSetName()
    {
        $renderer = new ShopierButtonRenderer($this->createPreparedShopier());
        $this->assertSame($renderer, $renderer->setName('Pay Securely with Shopier'));
        $renderer->render();
        $output = $renderer->output(true);

        $this->assertStringContainsString('>Pay Securely with Shopier</button>', $output);
        $this->assertStringNotContainsString('Shopier ile Güvenli Öde', $output);
    }

    public function testIframeRenderer()
    {
        $renderer = new IframeRenderer($this->createPreparedShopier());
        $renderer
            ->setWidth(600)
            ->setHeight('80vh')
            ->setCenter(false);
        $renderer->render();
        $output = $renderer->output(true);

        $this->assertStringContainsString(
            '<iframe id="shopier-payment-iframe" name="shopier-payment-iframe" ' .
            'style="width: 600px; height: 80vh; border: 0;"></iframe>',
            $output
        );
        $this->assertStringContainsString('target="shopier-payment-iframe"', $output);
        $this->assertStringContainsString('document.getElementById("shopier_payment_form").submit();', $output);
        $this->assertStringNotContainsString('display: flex', $output);
    }
}
