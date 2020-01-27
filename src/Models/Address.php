<?php


namespace Shopier\Models;

/**
 * Class Address
 * @package Shopier\Models
 *
 * Common address for both shipping and billing address
 *
 */
class Address extends BaseModel
{
    protected static $requirement = [
        'address' => true,
        'city' => true,
        'country' => true,
        'postcode' => true,
    ];

    /** @var string */
    public $address;

    /** @var string */
    public $city;

    /** @var string */
    public $country;

    /** @var string */
    public $postcode;
}