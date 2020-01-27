<?php


namespace Shopier;


use Shopier\Exceptions\NotRendererClassException;
use Shopier\Exceptions\RendererClassNotFoundException;
use Shopier\Exceptions\RequiredParameterException;
use Shopier\Models\ShopierParams;
use Shopier\Models\ShopierResponse;
use Shopier\Renderers\AbstractRenderer;

/**
 * Class Shopier
 * @package Shopier
 *
 * Shopier Payment API gateway main class
 */
class Shopier
{
    /** @var string */
    protected $payment_url = 'https://www.shopier.com/ShowProduct/api_pay4.php';

    /** @var ShopierParams */
    protected $params;

    /** @var @var string */
    private $api_key;

    /** @var string */
    private $api_secret;

    public function __construct($api_key, $api_secret, ShopierParams $shopierParams = null)
    {
        $this->setApiKey($api_key);
        $this->setApiSecret($api_secret);
        $this->setParams($shopierParams ? $shopierParams : new ShopierParams());
        $this->params->setAPIKey($api_key);
    }

    /**
     * @return string
     */
    public function getPaymentUrl()
    {
        return $this->payment_url;
    }

    /**
     * @param string $payment_url
     */
    public function setPaymentUrl($payment_url)
    {
        $this->payment_url = $payment_url;
    }

    /**
     * @return mixed
     */
    public function getApiKey()
    {
        return $this->api_key;
    }

    /**
     * @param mixed $api_key
     */
    public function setApiKey($api_key)
    {
        $this->api_key = $api_key;
    }

    /**
     * @return string
     */
    public function getApiSecret()
    {
        return $this->api_secret;
    }

    /**
     * @param string $api_secret
     */
    public function setApiSecret($api_secret)
    {
        $this->api_secret = $api_secret;
    }

    /**
     * @param $platform_order_id
     * @param $total_order_value
     * @param null $callback
     * @return Shopier
     * @deprecated use Shopier\Models\ShopierParams::setOrderData
     */
    public function setOrderData($platform_order_id, $total_order_value, $callback = null)
    {
        $this->getParams()->setOrderData($platform_order_id, $total_order_value, $callback);
        return $this;
    }

    /**
     * @return ShopierParams
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param ShopierParams $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * @param $product_name
     * @param null $product_type
     * @return Shopier
     * @deprecated use Shopier\Models\ShopierParams::setProductData
     */
    public function setProductData($product_name, $product_type = null)
    {
        $this->getParams()->setProductData($product_name, $product_type);
        return $this;
    }

    /**
     * @param array $responseData
     * @return bool
     */
    public function validateResponse(array $responseData = [])
    {
        return ShopierResponse::fromArray($responseData ? $responseData : $_POST)->hasValidSignature($this->getApiSecret());
    }

    /**
     * Go to Shopier Payment Page automatically
     *
     * @throws Exceptions\RequiredParameterException
     */
    public function go()
    {
        $this->prepare();

        $inputs = '';
        foreach ($this->params->toArray() as $key => $value) {
            $inputs .= <<<END
<input type="hidden" name="$key" value="$value">

END;
        }

        $form = <<<END
<form id="shopier_payment_form" method="post" action="{$this->payment_url}">
{$inputs}
</form>
END;


        echo <<<END
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title></title>
</head>
<body>
{$form}
<script type="text/javascript">
    document.getElementById("shopier_payment_form").submit();
</script>
</body>
</html>
END;
    }

    /**
     * @throws Exceptions\RequiredParameterException
     */
    public function prepare()
    {
        $this->calculateSignature();
        $this->params->validate();
    }

    /**
     * @return $this
     */
    public function calculateSignature()
    {
        $params = $this->getParams();
        $signature = base64_encode(hash_hmac('SHA256', $params->getDataToBeHashed(), $this->api_secret, true));
        $params->setSignature($signature);

        return $this;
    }

    /**
     * @param $rendererClass
     * @return AbstractRenderer
     * @throws NotRendererClassException
     * @throws RendererClassNotFoundException
     */
    public function createRenderer($rendererClass)
    {
        if (!class_exists($rendererClass))
            throw new RendererClassNotFoundException("Renderer class not found: $rendererClass");
        /** @var AbstractRenderer $renderer */
        $renderer = new $rendererClass($this);
        if (!($renderer instanceof AbstractRenderer))
            throw new NotRendererClassException("This class is not renderer: $rendererClass");

        return $renderer;
    }

    /**
     * @param AbstractRenderer $renderer
     * @param bool $return
     * @param bool $die
     * @return string
     * @throws RequiredParameterException
     */
    public function goWith(AbstractRenderer $renderer, $return = false, $die = false)
    {
        $renderer->render();
        return $renderer->output($return, $die);
    }

}