<?php
declare(strict_types = 1);

namespace Wilensky\AdyenBundle\Entities;

use Wilensky\AdyenBundle\Traits\MerchantSignatureTrait as MST;

/**
 * Adyen payment form data entity
 * @author Gregg Wilensky <https://github.com/wilensky/>
 */
class PaymentData extends RecurringPaymentRequest
{
    use MST;

    const DEFAULT_SESSION_VALIDITY_INTERVAL = 'P1D';
    const DEFAULT_SHIP_BEFORE_DATE_INTERVAL = 'P7D';

    const FIELD_SESSION_VALIDITY = 'sessionValidity';
    const FIELD_SHIP_BEFORE_DATE = 'shipBeforeDate';
    const FIELD_ORDER_DATA = 'orderData';
    const FIELD_SKIN_CODE = 'skinCode';
    const FIELD_MERCHANT_RETURN_DATA = 'merchantReturnData';
    const FIELD_MERCHANT_SIG = 'merchantSig';
    const FIELD_MERCHANT_REFERENCE = 'merchantReference';
    const FIELD_RECURRING_CONTRACT = 'recurringContract';
    const FIELD_PAYMENT_AMOUNT = 'paymentAmount';
    const FIELD_CURRENCY_CODE = 'currencyCode';

    /**
     * Proxy method for same field in current class
     * @param string $v
     * @return PaymentData
     */
    public function setRecurring(string $v = self::CONTRACT_RECURRING)
    {
        return $this->setRecurringContract($v);
    }

    /**
     * Proxy method for same fields in current class
     * @param float $amount
     * @param string $currency
     * @return PaymentData
     */
    public function setAmount(float $amount, string $currency)
    {
        return $this->setPaymentAmount((int)($amount * 100))->setCurrencyCode($currency);
    }

    /**
     * NOOP for `shopperInteraction` field bec. it is not applicable for this class
     * @param type $v
     * @return \PB\CPanelBundle\Adyen\Entities\PaymentData
     */
    public function setShopperInteraction(string $v = self::SI_CONT_AUTH): PaymentData
    {
        return $this;
    }

    /**
     * Increases session validity time by provided DTI spec
     * @param string $interval \DateInterval interval spec
     */
    public function increaseSessionValidity($interval = null)
    {
        $dto = \DateTime::createFromFormat('c', $this->getSessionValidity()) ?: new \DateTime();
        return $this->setSessionValidity(
            $dto->add(new \DateInterval($interval ?: self::DEFAULT_SESSION_VALIDITY_INTERVAL))
        );
    }

    /**
     * The final time by which a payment needs to have been made.
     * This is especially useful for tickets/reservations, where you want to `lock`
     *  the item for sale for only a short time and payments made after this time
     *  would lead to extra costs and administrative hassle.
     * @param \DateTime $dto
     * @return PaymentData
     */
    public function setSessionValidity(\DateTime $dto)
    {
        return $this->addData(self::FIELD_SESSION_VALIDITY, $dto->format('c')); // YYYY-MMDDThh:mm:ssTZD
    }

    protected function getSessionValidity(): string
    {
        return (string)$this->getData(self::FIELD_SESSION_VALIDITY);
    }

    /**
     * Increases session validity time by provided DTI spec
     * @param string $interval \DateInterval interval spec
     */
    public function increaseShipBeforeDate($interval = null)
    {
        $dto = \DateTime::createFromFormat('Y-m-d', $this->getShipBeforeDate()) ?: new \DateTime();
        return $this->setShipBeforeDate(
            $dto->add(new \DateInterval($interval ?: self::DEFAULT_SHIP_BEFORE_DATE_INTERVAL))
        );
    }

    /**
     * The date by which the goods or services specified in the order must be shipped or rendered.
     * @param \DateTime $dto YYYY-MM-DD
     * @return PaymentData
     */
    public function setShipBeforeDate(\DateTime $dto)
    {
        return $this->addData(self::FIELD_SHIP_BEFORE_DATE, $dto->format('Y-m-d'));
    }

    protected function getShipBeforeDate(): string
    {
        return (string)$this->getData(self::FIELD_SHIP_BEFORE_DATE);
    }

    /**
     * A fragment of HTML which will be displayed to the customer on a `review payment` page just before final confirmation of the payment.
     * In order to guarantee correct transmission of this data, including the sending of non-western characters (e.g. the Japanese or Cyrillic character sets),
     *  the data is compressed and encoded in the session (GZIP compressed and Base64 encoded).
     * We provide code examples in common programming languages for this (see link in the Introduction).
     * @param string $v HTML
     * @return PaymentData
     */
    public function setOrderData(string $v)
    {
        return $this->addData(self::FIELD_ORDER_DATA, base64_encode(gzencode($v)));
    }

    /**
     * This field will be passed back as-is on the return URL when the shopper completes (or abandons) the
     * payment and returns to your shop. Typically used to transmit a session ID (max. 128 characters).
     * N.B. Adyen cannot guarantee that all payment methods will work when using the merchantReturnData parameter.
     * Especially for redirect payment methods, such as iDEAL, the merchantReturnData parameter is added to the request which may have a limited size.
     * If, with the merchantReturnData, the size of the request is too large, the payment can fail.
     * @param type $v
     * @return type
     */
    public function setMerchantReturnData(string $v)
    {
        return $this->addData(self::FIELD_MERCHANT_RETURN_DATA, $v);
    }

    /**
     * The (merchant) reference for this payment.
     * This reference will be used in all communication to the merchant about the status of the payment.
     * Although it is a good idea to make sure it is unique, this is not a requirement.
     * The maximum length is 80 characters.
     * @param string $v
     * @return PaymentData
     */
    public function setMerchantReference(string $v)
    {
        $maxLength = 80;
        if (strlen($v) > $maxLength) {
            throw new \RuntimeException('Maximum length of ' . $maxLength . ' exceeded for merchant reference');
        }

        return $this->addData(self::FIELD_MERCHANT_REFERENCE, $v);
    }

    /**
     * What type of recurring contract is used.
     * For the CVC-Only payments the value ONECLICK should be used.
     * @param string $v
     * @return PaymentData
     */
    public function setRecurringContract(string $v)
    {
        return $this->addData(self::FIELD_RECURRING_CONTRACT, $v);
    }

    /**
     * The code of the skin to be used.
     * You can have more than one skin associated with your account if you require a different branding.
     * @param string $v
     * @return PaymentData
     */
    public function setSkinCode(string $v)
    {
        return $this->addData(self::FIELD_SKIN_CODE, $v);
    }

    /**
     * The payment amount specified in minor units (without decimal separator).
     * For example GBP100 is specified as 10000 and EUR199.95 is specified as 19995.
     * Most currencies are like this and have 100 minor units to a major unit (e.g. pennies to the pound, cents to the Euro).
     * However the Japanese yen is an exception and doesn't have any minor units (e.g. 1001 yen is specified as 1001).
     * @param float $v
     * @return PaymentData
     */
    public function setPaymentAmount(int $v)
    {
        return $this->addData(self::FIELD_PAYMENT_AMOUNT, $v);
    }

    /**
     * The three-letter capitalized ISO currency code to pay in.
     * @param string $v
     * @return PaymentData
     */
    public function setCurrencyCode(string $v)
    {
        return $this->addData(self::FIELD_CURRENCY_CODE, strtoupper((string)$v));
    }

    /**
     * The signature in Base64 encoded format. The signature is generated by concatenating the values of a
     *  number of the payment session fields and computing the HMAC over this using the shared secret (configured in the skin).
     * See Appendix B for details on computing the signature.
     * @param string $hmac_key
     * @return PaymentData
     */
    public function calculateMerchantSig(string $hmac_key)
    {
        return strlen($hmac_key) === 64
            ? $this->calculateSHA256($hmac_key)
            : $this->calculateSHA1($hmac_key);
    }

    /**
     * @param string $hmac_key
     * @return $this
     */
    private function calculateSHA256(string $hmac_key)
    {
        return $this->addData(
            self::FIELD_MERCHANT_SIG,
            $this->SHA256signature($hmac_key, $this->getData())
        );
    }

    /**
     * @param string $hmac_key
     * @return $this
     */
    private function calculateSHA1(string $hmac_key)
    {
        return $this->addData(
            self::FIELD_MERCHANT_SIG,
            $this->SHA1signature($hmac_key, [
                self::FIELD_PAYMENT_AMOUNT,
                self::FIELD_CURRENCY_CODE,
                self::FIELD_SHIP_BEFORE_DATE,
                self::FIELD_MERCHANT_REFERENCE,
                self::FIELD_SKIN_CODE,
                parent::FIELD_MERCHANT_ACCOUNT,
                self::FIELD_SESSION_VALIDITY,
                parent::FIELD_SHOPPER_EMAIL,
                parent::FIELD_SHOPPER_REFERENCE,
                self::FIELD_RECURRING_CONTRACT,
                self::FIELD_MERCHANT_RETURN_DATA
            ])
        );
    }
}