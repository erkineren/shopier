<?php

namespace Shopier\Renderers;

use Shopier\Models\ShopierParams;
use Shopier\Shopier;

abstract class AbstractRenderer
{
    /**
     * @var Shopier
     */
    protected $shopier;

    /**
     * @var ShopierParams
     */
    protected $params;

    /**
     * @var string
     */
    protected $data = '';

    /**
     * AbstractRenderer constructor.
     * @param Shopier $shopier
     */
    public function __construct(Shopier $shopier)
    {
        $this->shopier = $shopier;
        $this->params = $shopier->getParams();
    }

    abstract public function render();

    /**
     * Escapes a value for safe usage inside HTML content or attributes.
     *
     * @param mixed $value
     * @return string
     */
    protected static function escape($value)
    {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public function output($return = false, $exit = false)
    {
        if (!$return) {
            echo $this->data;
        }
        if ($exit) {
            exit();
        }
        return $this->data;
    }
}
