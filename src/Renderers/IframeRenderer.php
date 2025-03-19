<?php

namespace Shopier\Renderers;

class IframeRenderer extends FormRenderer
{
    protected $width = '600px';
    protected $height = '600px';
    protected $center = true;

    public function render()
    {
        parent::render();

        $frameHtml = <<<EOL
<!DOCTYPE html>
<html lang="tr">
<body>
    {$this->data}
    <script type="text/javascript">
        document.getElementById("shopier_payment_form").submit();
    <\/script>
</body>
</html>
EOL;
        $center = $this->center ? 'height:100%; display: flex; justify-content: center; align-items: center;' : '';
        $this->data = <<<END
<div style="$center">
<iframe 
id="shopier-payment-iframe" 
name="shopier-payment-iframe"
src="#"
style="width: $this->width; height: $this->height; border: 0;" 
>
</iframe>
<script type="text/javascript">
   var docHtml = `$frameHtml`;
   var doc = document.getElementById('shopier-payment-iframe').contentWindow.document;
   doc.open();
   doc.write(docHtml);
   doc.close();
   document.getElementById("shopier_payment_form").submit();
</script>
</div>

END;
    }

    /**
     * @return string
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param string $width
     * @return IframeRenderer
     */
    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @return string
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param string $height
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
