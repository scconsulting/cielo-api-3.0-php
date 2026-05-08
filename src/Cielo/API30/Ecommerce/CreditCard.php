<?php

namespace Cielo\API30\Ecommerce;

/**
 * Class CreditCard
 *
 * @package Cielo\API30\Ecommerce
 */
class CreditCard implements \JsonSerializable, CieloSerializable
{
    /**
     * Bandeira Visa
     */
    const VISA = 'Visa';

    /**
     * Bandeira Mastercard
     */
    const MASTERCARD = 'Master';

    /**
     * Bandeira American Express
     */
    const AMEX = 'Amex';

    /**
     * Bandeira ELO
     */
    const ELO = 'Elo';

    /**
     * Bandeira Aura
     */
    const AURA = 'Aura';

    /**
     * Bandeira JCB
     */
    const JCB = 'JCB';

    /**
     * Bandeira Diners
     */
    const DINERS = 'Diners';

    /**
     * Bandeira Discover
     */
    const DISCOVER = 'Discover';

    /**
     * Bandeira Hipercard
     */
    const HIPERCARD = 'Hipercard';

    /** @var string $cardNumber */
    private $cardNumber;

    /** @var string $holder */
    private $holder;

    /** @var string $expirationDate */
    private $expirationDate;

    /** @var string $securityCode */
    private $securityCode;

    /** @var bool $saveCard */
    private $saveCard = false;

    /** @var string $brand */
    private $brand;

    /** @var string $cardToken */
    private $cardToken;

    /** @var string $customerName */
    private $customerName;

    /** @var \stdClass $links */
    private $links;

    /**
     * Detect card brand from card number using BIN patterns.
     * Returns one of the brand constants or null if unrecognised.
     *
     * @param string $cardNumber digits only (spaces/dots stripped by caller)
     * @return string|null
     */
    public static function detectBrand($cardNumber)
    {
        $number = preg_replace('/\D/', '', $cardNumber);

        // Elo (Brazilian) — checked before Visa/Master due to BIN overlap
        if (preg_match('/^(4011(67|78|79)|43(1274|8935)|45(1416|7393|763(1|2))|504175|627780|636297|636368|6504(0[5-9]|1[0-9]|2[0-9]|3[0-9])|6505(4[0-9]|5[0-9]|6[0-9]|7[0-9]|8[0-9]|9[0-9])|6507(0[0-9]|1[0-8])|6509(0[1-9]|1[0-9]|20)|6516(5[2-9]|6[0-9]|7[0-9])|6550(0[0-9]|1[0-9]|2[1-9]|3[0-9]|4[0-9]|5[0-8]))/', $number)) {
            return self::ELO;
        }

        // Hipercard (Brazilian)
        if (preg_match('/^(606282|637095|637568|637599|637612|637609)/', $number)) {
            return self::HIPERCARD;
        }

        // Aura (Brazilian)
        if (preg_match('/^50[0-9]/', $number)) {
            return self::AURA;
        }

        // Amex
        if (preg_match('/^3[47]/', $number)) {
            return self::AMEX;
        }

        // Diners
        if (preg_match('/^3(0[0-5]|[68])/', $number)) {
            return self::DINERS;
        }

        // Discover
        if (preg_match('/^6(011|4[4-9]|5)/', $number)) {
            return self::DISCOVER;
        }

        // JCB
        if (preg_match('/^35(2[89]|[3-8][0-9])/', $number)) {
            return self::JCB;
        }

        // Mastercard (classic 51-55 and new 2221-2720 range)
        if (preg_match('/^(5[1-5]|2(2[2-9][1-9]|[3-6][0-9]{3}|7[01][0-9]|720))/', $number)) {
            return self::MASTERCARD;
        }

        // Visa
        if (preg_match('/^4/', $number)) {
            return self::VISA;
        }

        return null;
    }

    /**
     * @param string $json
     *
     * @return CreditCard
     */
    public static function fromJson($json)
    {
        $object    = \json_decode($json);
        $cardToken = new CreditCard();
        $cardToken->populate($object);

        return $cardToken;
    }

    /**
     * @inheritdoc
     */
    public function populate(\stdClass $data)
    {
        $this->cardNumber     = isset($data->CardNumber) ? $data->CardNumber : null;
        $this->holder         = isset($data->Holder) ? $data->Holder : null;
        $this->expirationDate = isset($data->ExpirationDate) ? $data->ExpirationDate : null;
        $this->securityCode   = isset($data->SecurityCode) ? $data->SecurityCode : null;
        $this->saveCard       = isset($data->SaveCard) ? !!$data->SaveCard : false;
        $this->brand          = isset($data->Brand) ? $data->Brand : null;
        $this->cardToken      = isset($data->CardToken) ? $data->CardToken : null;
        $this->links          = isset($data->Links) ? $data->Links : new \stdClass();
        $this->customerName   = isset($data->CustomerName) ? $data->CustomerName : null;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    /**
     * @return mixed
     */
    public function getCardNumber()
    {
        return $this->cardNumber;
    }

    /**
     * @param $cardNumber
     *
     * @return $this
     */
    public function setCardNumber($cardNumber)
    {
        $this->cardNumber = $cardNumber;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getHolder()
    {
        return $this->holder;
    }

    /**
     * @param $holder
     *
     * @return $this
     */
    public function setHolder($holder)
    {
        $this->holder = $holder;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * @param $expirationDate
     *
     * @return $this
     */
    public function setExpirationDate($expirationDate)
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSecurityCode()
    {
        return $this->securityCode;
    }

    /**
     * @param $securityCode
     *
     * @return $this
     */
    public function setSecurityCode($securityCode)
    {
        $this->securityCode = $securityCode;

        return $this;
    }

    /**
     * @return bool
     */
    public function getSaveCard()
    {
        return $this->saveCard;
    }

    /**
     * @param $saveCard
     *
     * @return $this
     */
    public function setSaveCard($saveCard)
    {
        $this->saveCard = $saveCard;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @param $brand
     *
     * @return $this
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCardToken()
    {
        return $this->cardToken;
    }

    /**
     * @param $cardToken
     *
     * @return $this
     */
    public function setCardToken($cardToken)
    {
        $this->cardToken = $cardToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerName()
    {
        return $this->customerName;
    }

    /**
     * @param string $customerName
     */
    public function setCustomerName($customerName)
    {
        $this->customerName = $customerName;
    }

    /**
     * @return \stdClass
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * @param \stdClass $links
     */
    public function setLinks($links)
    {
        $this->links = $links;
    }
}
