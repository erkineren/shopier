<?php


namespace Shopier\Models;


class Buyer extends BaseModel
{
    protected static $requirement = [
        'id' => true,
        'name' => true,
        'surname' => true,
        'email' => true,
        'phone' => true,
        'account_age' => false,
    ];

    /** @var string */
    public $id;
    /** @var string */
    public $name;
    /** @var string */
    public $surname;
    /** @var string */
    public $email;
    /** @var string */
    public $phone;
    /** @var int */
    public $account_age = 0;


}