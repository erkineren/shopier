<?php

namespace Shopier\Models;

use Shopier\Enums\Currency;
use Shopier\Enums\Language;
use Shopier\Enums\ProductType;
use Shopier\Enums\WebsiteIndex;


class ShopierParams extends BaseModel
{

    protected static $requirement = [
        'API_key' => true,
        'website_index' => true,
        'platform_order_id' => true,
        'product_name' => false,
        'product_type' => true,
        'buyer_name' => true,
        'buyer_surname' => true,
        'buyer_email' => true,
        'buyer_account_age' => true,
        'buyer_id_nr' => true,
        'buyer_phone' => true,
        'billing_address' => true,
        'billing_city' => true,
        'billing_country' => true,
        'billing_postcode' => true,
        'shipping_address' => true,
        'shipping_city' => true,
        'shipping_country' => true,
        'shipping_postcode' => true,
        'total_order_value' => true,
        'currency' => true,
        'platform' => true,
        'is_in_frame' => true,
        'current_language' => true,
        'modul_version' => true,
        'random_nr' => true,
        'signature' => true,
        'callback' => false,
    ];

    /** @var string */
    public $API_key;

    /** @var int */
    public $website_index = WebsiteIndex::SITE_1;

    /** @var string */
    public $platform_order_id;

    /** @var string */
    public $product_name;

    /** @var int */
    public $product_type = ProductType::DEFAULT_TYPE;

    /** @var string */
    public $buyer_name;

    /** @var string */
    public $buyer_surname;

    /** @var string */
    public $buyer_email;

    /** @var int */
    public $buyer_account_age = 0;

    /** @var int */
    public $buyer_id_nr;

    /** @var string */
    public $buyer_phone;

    /** @var string */
    public $billing_address;

    /** @var string */
    public $billing_city;

    /** @var string */
    public $billing_country;

    /** @var string */
    public $billing_postcode;

    /** @var string */
    public $shipping_address;

    /** @var string */
    public $shipping_city;

    /** @var string */
    public $shipping_country;

    /** @var string */
    public $shipping_postcode;

    /** @var string */
    public $total_order_value;

    /** @var int */
    public $currency = Currency::TL;

    /** @var int */
    public $platform = 0;

    /** @var int */
    public $is_in_frame = 0;

    /** @var int */
    public $current_language = Language::TR;

    /** @var string */
    public $modul_version = '1.0.4';

    /** @var int */
    public $random_nr;

    /** @var string */
    public $signature;

    /** @var string */
    public $callback;

    /**
     * ShopierParams constructor.
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        $this->random_nr = rand(100000, 999999);
        parent::__construct($values);
    }

    /**
     * @param Buyer $buyer
     * @return $this
     */
    public function setBuyer(Buyer $buyer)
    {
        $this->buyer_id_nr = $buyer->id;
        $this->buyer_name = $buyer->name;
        $this->buyer_surname = $buyer->surname;
        $this->buyer_email = $buyer->email;
        $this->buyer_phone = $buyer->phone;
        $this->buyer_account_age = $buyer->account_age;

        return $this;
    }

    /**
     * @param Address $address
     */
    public function setAddress(Address $address)
    {
        $this->setBillingAddress($address, true);
    }

    /**
     * @param Address $address
     * @param bool $setAlsoShippingAdress
     * @return $this
     */
    public function setBillingAddress(Address $address, $setAlsoShippingAdress = false)
    {
        $this->billing_address = $address->address;
        $this->billing_city = $address->city;
        $this->billing_country = $address->country;
        $this->billing_postcode = $address->postcode;

        if ($setAlsoShippingAdress) $this->setShippingAddress($address);

        return $this;
    }

    /**
     * @param Address $address
     * @param bool $setAlsoBillingAdress
     * @return $this
     */
    public function setShippingAddress(Address $address, $setAlsoBillingAdress = false)
    {
        $this->shipping_address = $address->address;
        $this->shipping_city = $address->city;
        $this->shipping_country = $address->country;
        $this->shipping_postcode = $address->postcode;

        if ($setAlsoBillingAdress) $this->setBillingAddress($address);

        return $this;
    }

    /**
     * @return string
     */
    public function getDataToBeHashed()
    {
        return $this->random_nr . $this->platform_order_id . $this->total_order_value . $this->currency;
    }
}
