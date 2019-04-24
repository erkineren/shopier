<?php


namespace Shopier\Renderers;


class ButtonRenderer extends FormRenderer
{
    protected $attributes = [];
    protected $inner_html = '';


    public function withId($id)
    {
        $this->attributes['id'] = $id;
        return $this;
    }

    public function withName($name)
    {
        $this->attributes['name'] = $name;
        return $this;
    }

    public function withText($text)
    {
        $this->inner_html = $text;
        return $this;
    }

    public function withClass($class)
    {
        $this->attributes['class'] = $class;
        return $this;
    }

    public function withStyle($style)
    {
        $this->attributes['style'] = $style;
        return $this;
    }

    public function withCustom($attr, $value)
    {
        $this->attributes[$attr] = $value;
        return $this;
    }

    public function render()
    {
        $attributes = [];
        foreach ($this->attributes as $key => $value) {
            $attributes[] = $key . '="' . $value . '"';
        }
        $attribute_str = implode(' ', $attributes);

        $button = <<<END
<button type="submit" {$attribute_str}>{$this->inner_html}</button>
END;

        $this->form_end = $button;
        parent::render();
    }


}