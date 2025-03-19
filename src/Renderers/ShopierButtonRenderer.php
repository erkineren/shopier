<?php

namespace Shopier\Renderers;

use Shopier\Shopier;

class ShopierButtonRenderer extends ButtonRenderer
{
    protected $name = 'Shopier ile Güvenli Öde';

    /**
     * ShopierButtonRenderer constructor.
     * @param Shopier $shopier
     */
    public function __construct(Shopier $shopier)
    {
        parent::__construct($shopier);
        $this->withStyle("padding:15px; color: #fff; background-color:#51cbb0; border:1px solid #fff; border-radius:7px; cursor: pointer;")
            ->withText($this->name);
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}
