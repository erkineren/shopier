<?php

namespace Shopier\Renderers;

class IframeRenderer extends FormRenderer
{
    /** @var string */
    protected $iframe_name = 'shopier-payment-iframe';

    /** @var string|int */
    protected $width = '600px';

    /** @var string|int */
    protected $height = '600px';

    /** @var bool */
    protected $center = true;

    public function render()
    {
        $this->form_target = $this->iframe_name;
        $this->form_end = <<<END
<script type="text/javascript">
    document.getElementById("shopier_payment_form").submit();
</script>
END;

        parent::render();

        $name = self::escape($this->iframe_name);
        $width = self::escape(self::toCssSize($this->width));
        $height = self::escape(self::toCssSize($this->height));
        $center = $this->center ? 'height:100%; display: flex; justify-content: center; align-items: center;' : '';

        $this->data = <<<END
<div style="$center">
<iframe id="$name" name="$name" style="width: $width; height: $height; border: 0;"></iframe>
</div>
{$this->data}

END;
    }

    /**
     * Converts numeric sizes (e.g. 600) to pixel values (e.g. "600px").
     *
     * @param string|int $size
     * @return string
     */
    protected static function toCssSize($size)
    {
        return is_numeric($size) ? $size . 'px' : (string)$size;
    }

    /**
     * @return string|int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param string|int $width Numeric values are treated as pixels
     * @return IframeRenderer
     */
    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @return string|int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param string|int $height Numeric values are treated as pixels
     * @return IframeRenderer
     */
    public function setHeight($height)
    {
        $this->height = $height;
        return $this;
    }

    /**
     * @return bool
     */
    public function isCenter()
    {
        return $this->center;
    }

    /**
     * @param bool $center
     * @return IframeRenderer
     */
    public function setCenter($center)
    {
        $this->center = $center;
        return $this;
    }
}
