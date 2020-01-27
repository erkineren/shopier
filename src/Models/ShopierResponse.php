<?php


namespace Shopier\Models;


use Shopier\Exceptions\RequiredParameterException;

class ShopierResponse extends BaseModel
{
    protected static $requirement = [
        'platform_order_id' => true,
        'status' => true,
        'installment' => true,
        'payment_id' => true,
        'random_nr' => true,
        'signature' => true,
    ];

    /** @var string */
    protected $platform_order_id;

    /** @var string */
    protected $API_key;

    /** @var string */
    protected $status;

    /** @var int */
    protected $installment;

    /** @var int */
    protected $payment_id;

    /** @var int */
    protected $random_nr;

    /** @var string */
    protected $signature;

    public static function fromPostData()
    {
        return new static($_POST);
    }

    public function __construct(array $values = [])
    {
        parent::__construct($values);
    }

    /**
     * @return string
     */
    public function getPlatformOrderId()
    {
        return $this->platform_order_id;
    }

    /**
     * @return string
     */
    public function getAPIKey()
    {
        return $this->API_key;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return $this->getStatus() == 'success';
    }

    /**
     * @return int
     */
    public function getInstallment()
    {
        return $this->installment;
    }

    /**
     * @return int
     */
    public function getPaymentId()
    {
        return $this->payment_id;
    }

    /**
     * @return int
     */
    public function getRandomNr()
    {
        return $this->random_nr;
    }

    /**
     * @return string
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * @return string
     */
    public function getDecodedSignature()
    {
        return base64_decode($this->getSignature());
    }


    public function getExpectedSignature($apiSecret)
    {
        return hash_hmac('sha256', $this->getRandomNr() . $this->getPlatformOrderId(), $apiSecret, true);
    }

    /**
     * Example Response Array:
     * <code>
     * [
     * 'platform_order_id' => '10002',
     * 'status' => 'success',
     * 'installment' => '0',
     * 'payment_id' => '954344654',
     * 'random_nr' => '123456',
     * 'signature' => '+e1klzFG7ZABS16xnHcZ8peqbvSZD3Pv9NU4pWiw0qE=',
     * ]
     * </code>
     *
     * @param $apiSecret
     * @return bool
     */
    public function hasValidSignature($apiSecret)
    {
        try {
            $this->validate();
        } catch (RequiredParameterException $e) {
            return false;
        }

        return $this->getDecodedSignature() === $this->getExpectedSignature($apiSecret);
    }


}