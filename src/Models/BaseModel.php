<?php


namespace Shopier\Models;


use Shopier\Exceptions\RequiredParameterException;

abstract class BaseModel
{
    protected static $requirement = [];

    /**
     * BaseModel constructor.
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        $this->fillValues($values);
    }

    /**
     * @param array $properties
     * @param bool $ignoreNull
     * @return static
     */
    public function fillValues(array $properties, $ignoreNull = true)
    {
        foreach ($properties as $property => $value) {
            if (property_exists($this, $property)) {
                if ($ignoreNull && $value === null) continue;
                $this->{$property} = $value;
            }
        }
        return $this;
    }

    /**
     * @return array
     */
    public static function getOptionalParams()
    {
        return array_diff_key(static::$requirement, array_filter(static::$requirement));
    }

    /**
     * @param array $properties
     * @return BaseModel
     */
    public static function fromArray(array $properties)
    {
        return new static($properties);
    }

    /**
     * @return bool
     * @throws RequiredParameterException
     */
    public function validate()
    {
        $params = array_filter($this->toArray(), function ($param) {
            return $param !== null && $param !== '';
        });

        $diff = array_diff_key(static::getRequiredParams(), $params);

        if ($diff) {
            $str = implode(', ', array_keys($diff));
            throw new RequiredParameterException("Parameters are required : " . $str);
        }

        return true;
    }

    /**
     * @return mixed
     */
    public function toArray()
    {
        return get_object_vars($this);
    }

    /**
     * @return array
     */
    public static function getRequiredParams()
    {
        return array_filter(static::$requirement);
    }

    /**
     * @return false|string
     */
    public function toJson()
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}