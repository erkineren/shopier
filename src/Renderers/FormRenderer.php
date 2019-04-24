<?php


namespace Shopier\Renderers;


use Shopier\Exceptions\RequiredParameterException;

class FormRenderer extends AbstractRenderer
{
    protected $form_start = '';
    protected $form_end = '';

    /**
     * @throws RequiredParameterException
     */
    public function render()
    {
        $this->shopier->prepare();

        $inputs = '';
        foreach ($this->params->toArray() as $key => $value) {
            $inputs .= <<<END
<input type="hidden" name="$key" value="$value">

END;
        }

        $this->data = <<<END
<form id="shopier_payment_form" method="post" action="{$this->shopier->getPaymentUrl()}">
{$this->form_start}
{$inputs}
{$this->form_end}
</form>
END;
    }

}