<?php


namespace Shopier;


use Shopier\Exceptions\NotRendererClassException;
use Shopier\Exceptions\RendererClassNotFoundException;
use Shopier\Models\ShopierParams;
use Shopier\Renderers\AbstractRenderer;

/**
 * Class Shopier
 * @package Shopier
 *
 * Shopier Payment API gataway main class
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
        $this->api_key = $api_key;
        $this->api_secret = $api_secret;

        if ($shopierParams) $this->params = $shopierParams;
        else $this->params = new ShopierParams();

        $this->params->API_key = $api_key;
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

    public function setOrderData($platform_order_id, $total_order_value, $callback = null)
    {
        $this->params->fillValues([
            'platform_order_id' => $platform_order_id,
            'total_order_value' => $total_order_value,
            'callback' => $callback,
        ]);
    }

    public function setProductData($product_name, $product_type = null)
    {
        $this->params->fillValues([
            'product_name' => $product_name,
            'product_type' => $product_type
        ]);
    }

    /**
     * @param $response_data
     *
     * Example Response Data:
     * <code>
     * [
     * 'platform_order_id' => '10002',
     * 'API_key' => '************',
     * 'status' => 'success',
     * 'installment' => '0',
     * 'payment_id' => '954344654',
     * 'random_nr' => '123456',
     * 'signature' => '+e1klzFG7ZABS16xnHcZ8peqbvSZD3Pv9NU4pWiw0qE=',
     * ]
     * </code>
     *
     * @return bool
     */
    public function verifyResponse($response_data)
    {
        if (isset($response_data['platform_order_id']) && isset($response_data['random_nr']) && $response_data["signature"]) {
            $order_id = $response_data['platform_order_id'];
            $random_nr = $response_data['random_nr'];
            $signature = base64_decode($response_data["signature"]);

            if ($order_id && $random_nr && $signature) {
                $expected = hash_hmac('sha256', $random_nr . $order_id, $this->api_secret, true);
                if ($signature == $expected)
                    return true;
            }
        }
        return false;
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
        $params->signature = $signature;

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
     * @throws Exceptions\RequiredParameterException
     */
    public function goWith(AbstractRenderer $renderer, $return = false, $die = false)
    {
        $renderer->render();
        $renderer->output($return, $die);
    }

}