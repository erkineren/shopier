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
    protected $API_key;

    /** @var int */
    protected $website_index = WebsiteIndex::SITE_1;

    /** @var string */
    protected $platform_order_id;

    /** @var string */
    protected $product_name;

    /** @var int */
    protected $product_type = ProductType::DEFAULT_TYPE;

    /** @var string */
    protected $buyer_name;

    /** @var string */
    protected $buyer_surname;

    /** @var string */
    protected $buyer_email;

    /** @var int */
    protected $buyer_account_age = 0;

    /** @var int */
    protected $buyer_id_nr;

    /** @var string */
    protected $buyer_phone;

    /** @var string */
    protected $billing_address;

    /** @var string */
    protected $billing_city;

    /** @var string */
    protected $billing_country;

    /** @var string */
    protected $billing_postcode;

    /** @var string */
    protected $shipping_address;

    /** @var string */
    protected $shipping_city;

    /** @var string */
    protected $shipping_country;

    /** @var string */
    protected $shipping_postcode;

    /** @var string */
    protected $total_order_value;

    /** @var int */
    protected $currency = Currency::TL;

    /** @var int */
    protected $platform = 0;

    /** @var int */
    protected $is_in_frame = 0;

    /** @var int */
    protected $current_language = Language::TR;

    /** @var string */
    protected $modul_version = '1.0.4';

    /** @var int */
    protected $random_nr;

    /** @var string */
    protected $signature;

    /** @var string */
    protected $callback;

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
     * @return array
     */
    public static function getRequirement()
    {
        return self::$requirement;
    }

    /**
     * @param array $requirement
     */
    public static function setRequirement($requirement)
    {
        self::$requirement = $requirement;
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
     * @return ShopierParams
     */
    public function setAddress(Address $address)
    {
        return $this->setBillingAddress($address, true);
    }

    /**
     * @param Address $address
     * @param bool $setAlsoShippingAddress
     * @return ShopierParams
     */
    public function setBillingAddress(Address $address, $setAlsoShippingAddress = false)
    {
        $this->billing_address = $address->address;
        $this->billing_city = $address->city;
        $this->billing_country = $address->country;
        $this->billing_postcode = $address->postcode;


        if ($setAlsoShippingAddress) $this->setShippingAddress($address);

        return $this;
    }

    /**
     * @param Address $address
     * @param bool $setAlsoBillingAddress
     * @return ShopierParams
     */
    public function setShippingAddress(Address $address, $setAlsoBillingAddress = false)
    {
        $this->shipping_address = $address->address;
        $this->shipping_city = $address->city;
        $this->shipping_country = $address->country;
        $this->shipping_postcode = $address->postcode;

        if ($setAlsoBillingAddress) $this->setBillingAddress($address);

        return $this;
    }

    /**
     * @return string
     */
    public function getDataToBeHashed()
    {
        return $this->random_nr . $this->platform_order_id . $this->total_order_value . $this->currency;
    }

    /**
     * @param $platform_order_id
     * @param $total_order_value
     * @param null $callback
     * @return ShopierParams
     */
    public function setOrderData($platform_order_id, $total_order_value, $callback = null)
    {
        return $this->fillValues([
            'platform_order_id' => $platform_order_id,
            'total_order_value' => $total_order_value,
            'callback' => $callback,
        ]);
    }

    /**
     * @param $product_name
     * @param null $product_type
     * @return ShopierParams
     */
    public function setProductData($product_name, $product_type = null)
    {
        return $this->fillValues([
            'product_name' => $product_name,
            'product_type' => $product_type
        ]);
    }

    /**
     * @return string
     */
    public function getAPIKey()
    {
        return $this->API_key;
    }

    /**
     * @param string $API_key
     * @return ShopierParams
     */
    public function setAPIKey($API_key)
    {
        $this->API_key = $API_key;
        return $this;
    }

    /**
     * @return int
     */
    public function getWebsiteIndex()
    {
        return $this->website_index;
    }

    /**
     * @param int $website_index
     * @return ShopierParams
     */
    public function setWebsiteIndex($website_index)
    {
        $this->website_index = $website_index;
        return $this;
    }

    /**
     * @return string
     */
    public function getPlatformOrderId()
    {
        return $this->platform_order_id;
    }

    /**
     * @param string $platform_order_id
     * @return ShopierParams
     */
    public function setPlatformOrderId($platform_order_id)
    {
        $this->platform_order_id = $platform_order_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getProductName()
    {
        return $this->product_name;
    }

    /**
     * @param string $product_name
     * @return ShopierParams
     */
    public function setProductName($product_name)
    {
        $this->product_name = $product_name;
        return $this;
    }

    /**
     * @return int
     */
    public function getProductType()
    {
        return $this->product_type;
    }

    /**
     * @param int $product_type
     * @return ShopierParams
     */
    public function setProductType($product_type)
    {
        $this->product_type = $product_type;
        return $this;
    }

    /**
     * @return string
     */
    public function getBuyerName()
    {
        return $this->buyer_name;
    }

    /**
     * @param string $buyer_name
     * @return ShopierParams
     */
    public function setBuyerName($buyer_name)
    {
        $this->buyer_name = $buyer_name;
        return $this;
    }

    /**
     * @return string
     */
    public function getBuyerSurname()
    {
        return $this->buyer_surname;
    }

    /**
     * @param string $buyer_surname
     * @return ShopierParams
     */
    public function setBuyerSurname($buyer_surname)
    {
        $this->buyer_surname = $buyer_surname;
        return $this;
    }

    /**
     * @return string
     */
    public function getBuyerEmail()
    {
        return $this->buyer_email;
    }

    /**
     * @param string $buyer_email
     * @return ShopierParams
     */
    public function setBuyerEmail($buyer_email)
    {
        $this->buyer_email = $buyer_email;
        return $this;
    }

    /**
     * @return int
     */
    public function getBuyerAccountAge()
    {
        return $this->buyer_account_age;
    }

    /**
     * @param int $buyer_account_age
     * @return ShopierParams
     */
    public function setBuyerAccountAge($buyer_account_age)
    {
        $this->buyer_account_age = $buyer_account_age;
        return $this;
    }

    /**
     * @return int
     */
    public function getBuyerIdNr()
    {
        return $this->buyer_id_nr;
    }

    /**
     * @param int $buyer_id_nr
     * @return ShopierParams
     */
    public function setBuyerIdNr($buyer_id_nr)
    {
        $this->buyer_id_nr = $buyer_id_nr;
        return $this;
    }

    /**
     * @return string
     */
    public function getBuyerPhone()
    {
        return $this->buyer_phone;
    }

    /**
     * @param string $buyer_phone
     * @return ShopierParams
     */
    public function setBuyerPhone($buyer_phone)
    {
        $this->buyer_phone = $buyer_phone;
        return $this;
    }

    /**
     * @return string
     */
    public function getBillingCity()
    {
        return $this->billing_city;
    }

    /**
     * @param string $billing_city
     * @return ShopierParams
     */
    public function setBillingCity($billing_city)
    {
        $this->billing_city = $billing_city;
        return $this;
    }

    /**
     * @return string
     */
    public function getBillingCountry()
    {
        return $this->billing_country;
    }

    /**
     * @param string $billing_country
     * @return ShopierParams
     */
    public function setBillingCountry($billing_country)
    {
        $this->billing_country = $billing_country;
        return $this;
    }

    /**
     * @return string
     */
    public function getBillingPostcode()
    {
        return $this->billing_postcode;
    }

    /**
     * @param string $billing_postcode
     * @return ShopierParams
     */
    public function setBillingPostcode($billing_postcode)
    {
        $this->billing_postcode = $billing_postcode;
        return $this;
    }

    /**
     * @return string
     */
    public function getShippingCity()
    {
        return $this->shipping_city;
    }

    /**
     * @param string $shipping_city
     * @return ShopierParams
     */
    public function setShippingCity($shipping_city)
    {
        $this->shipping_city = $shipping_city;
        return $this;
    }

    /**
     * @return string
     */
    public function getShippingCountry()
    {
        return $this->shipping_country;
    }

    /**
     * @param string $shipping_country
     * @return ShopierParams
     */
    public function setShippingCountry($shipping_country)
    {
        $this->shipping_country = $shipping_country;
        return $this;
    }

    /**
     * @return string
     */
    public function getShippingPostcode()
    {
        return $this->shipping_postcode;
    }

    /**
     * @param string $shipping_postcode
     * @return ShopierParams
     */
    public function setShippingPostcode($shipping_postcode)
    {
        $this->shipping_postcode = $shipping_postcode;
        return $this;
    }

    /**
     * @return string
     */
    public function getTotalOrderValue()
    {
        return $this->total_order_value;
    }

    /**
     * @param string $total_order_value
     * @return ShopierParams
     */
    public function setTotalOrderValue($total_order_value)
    {
        $this->total_order_value = $total_order_value;
        return $this;
    }

    /**
     * @return int
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param int $currency
     * @return ShopierParams
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @return int
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * @param int $platform
     * @return ShopierParams
     */
    public function setPlatform($platform)
    {
        $this->platform = $platform;
        return $this;
    }

    /**
     * @return int
     */
    public function getIsInFrame()
    {
        return $this->is_in_frame;
    }

    /**
     * @param int $is_in_frame
     * @return ShopierParams
     */
    public function setIsInFrame($is_in_frame)
    {
        $this->is_in_frame = $is_in_frame;
        return $this;
    }

    /**
     * @return int
     */
    public function getCurrentLanguage()
    {
        return $this->current_language;
    }

    /**
     * @param int $current_language
     * @return ShopierParams
     */
    public function setCurrentLanguage($current_language)
    {
        $this->current_language = $current_language;
        return $this;
    }

    /**
     * @return string
     */
    public function getModulVersion()
    {
        return $this->modul_version;
    }

    /**
     * @param string $modul_version
     * @return ShopierParams
     */
    public function setModulVersion($modul_version)
    {
        $this->modul_version = $modul_version;
        return $this;
    }

    /**
     * @return int
     */
    public function getRandomNr()
    {
        return $this->random_nr;
    }

    /**
     * @param int $random_nr
     * @return ShopierParams
     */
    public function setRandomNr($random_nr)
    {
        $this->random_nr = $random_nr;
        return $this;
    }

    /**
     * @return string
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * @param string $signature
     * @return ShopierParams
     */
    public function setSignature($signature)
    {
        $this->signature = $signature;
        return $this;
    }

    /**
     * @return string
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @param string $callback
     * @return ShopierParams
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;
        return $this;
    }

}
