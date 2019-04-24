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


    public abstract function render();

    public function output($return = false, $die = false)
    {
        if ($return) return $this->data;
        echo $this->data;
        if ($die) die;
    }
}