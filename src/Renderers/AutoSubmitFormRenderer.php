<?php


namespace Shopier\Renderers;


class AutoSubmitFormRenderer extends FormRenderer
{
    public function render()
    {
        $this->form_end = <<<END
<script type="text/javascript">
    document.getElementById("shopier_payment_form").submit();
</script>
END;

        parent::render();
    }

}