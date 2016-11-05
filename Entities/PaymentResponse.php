<?php
declare(strict_types = 1);

namespace Wilensky\AdyenBundle\Entities;


use PB\PaymentBundle\Adyen\Traits\MerchantSignatureTrait;

use PB\PaymentBundle\Interfaces\{IPaymentResponse as IPR, IPaymentStrategyResult as IPSR};

use PB\PaymentBundle\Enum\PaymentMethodEnum as PMEnum;

/**
 * Entity contains Adyen payment response data
 * @author Gregg Wilensky <https://github.com/wilensky/>
 */
class PaymentResponse extends AbstractEntity implements IPR, IPSR
{
    use MerchantSignatureTrait;

    const AUTHORIZED = 'AUTHORISED';
    const REFUSED = 'REFUSED';
    const CANCELLED = 'CANCELLED';
    const PENDING = 'PENDING';
    const ERROR = 'ERROR';

    const FIELD_MERCHANT_REFERENCE = 'merchantReference';
    const FIELD_MERCHANT_RETURN_DATA = 'merchantReturnData';
    const FIELD_MERCHANT_SIG = 'merchantSig';
    const FIELD_SKIN_CODE = 'skinCode';
    const FIELD_SHOPPER_LOCALE = 'shopperLocale';
    const FIELD_PAYMENT_METHOD = 'paymentMethod';
    const FIELD_AUTH_RESULT = 'authResult';
    const FIELD_PSP_REFERENCE = 'pspReference';

    public function __construct(array $data = [], $withFields = false)
    {
        parent::__construct($data, ($withFields ? $this->getFields() : []));
    }

    public function getPaymentMethod(): string
    {
        return PMEnum::ADYENHPP;
    }
    
    /**
     * This reference you assigned to the original payment.
     * (e.g. Invoice reference)
     * @return string
     */
    public function getMerchantReference(): string
    {
        return (string)$this->getData(self::FIELD_MERCHANT_REFERENCE);
    }

    /**
     * If set in the payment session setup the value will be passed back as-is.
     * @return string
     */
    public function getMerchantReturnData(): string
    {
        return (string)$this->getData(self::FIELD_MERCHANT_RETURN_DATA);
    }

    /**
     * The signature computed over the above values in Base64 encoded format.
     * See Appendix B for details on computing the signature.
     * @return string
     */
    public function getMerchantSig()
    {
        return $this->getData(self::FIELD_MERCHANT_SIG);
    }

    /**
     * The code of the skin used.
     * @return string
     */
    public function getSkinCode(): string
    {
        return (string)$this->getData(self::FIELD_SKIN_CODE);
    }

    /**
     * Useful if you don't have the customer's language in-session.
     * (e.g. en_GB for (British) English)
     * @return string
     */
    public function getShopperLocale(): string
    {
        return (string)$this->getData(self::FIELD_SHOPPER_LOCALE);
    }

    /**
     * The payment method used.
     * For CANCELLED results the payment method may not be known and will therefore be empty
     * @return string
     */
    public function getCardType(): string
    {
        return (string)$this->getData(self::FIELD_PAYMENT_METHOD);
    }

    /**
     * Authorization result
     * @return string
     */
    public function getAuthResult(): string
    {
        return (string)$this->getData(self::FIELD_AUTH_RESULT);
    }

    /**
     * The reference we have assigned to the payment. This is guaranteed to be globally unique and is used
     *  when communicating with us about this payment. For PENDING, ERROR and CANCELLED results the
     *  `pspReference` may not (yet) be known and will therefore be empty.
     * @return string
     */
    public function getPspReference(): string
    {
        return (string)$this->getData(self::FIELD_PSP_REFERENCE);
    }

    private function matchAuthResult(string $value): bool
    {
        return strtoupper($this->getAuthResult()) === strtoupper($value);
    }

    /**
     * Payment authorization was successfully completed.
     * @return boolean
     */
    public function isAuthorized(): bool
    {
        return $this->matchAuthResult(self::AUTHORIZED);
    }

    /**
     * Payment was refused / payment authorization was unsuccessful.
     * @return boolean
     */
    public function isRefused(): bool
    {
        return $this->matchAuthResult(self::REFUSED);
    }

    /**
     * Final status of the payment attempt could not be established immediately.
     * This can happen if the systems providing final payment status are unavailable
     *  or the shopper needs to take further action to complete the payment.
     * @return boolean
     */
    public function isPending(): bool
    {
        return $this->matchAuthResult(self::PENDING);
    }

    /**
     * Did error occurred
     * @return boolean
     */
    public function isError(): bool
    {
        return $this->matchAuthResult(self::ERROR);
    }

    /**
     * Payment attempt was canceled by the shopper or the shopper requested
     *  to return to the merchant (e.g. by pressing the back button on the initial page).
     * @return boolean
     */
    public function isCancelled(): bool 
    {
        return $this->matchAuthResult(self::CANCELLED);
    }

    public function isFailed(): bool
    {
        return !$this->isAuthorized() || $this->isCancelled() || $this->isRefused() || $this->isError();
    }
    
    public function isSuccessful(): bool
    {
        return ($this->isAuthorized() || $this->isPending()) && !$this->isCancelled() && !$this->isRefused() && !$this->isError();
    }
    
    /**
     * @param string $hmac_key
     * @return string
     */
    protected function calculateMerchantSig(string $hmac_key): string
    {
        return strlen($hmac_key) === 64
            ? $this->calculateSHA256($hmac_key)
            : $this->calculateSHA1($hmac_key);
    }

    private function calculateSHA256(string $hmac_key): string
    {
        $data = array_filter($this->getData(), function ($v) {
            return !($v === null);
        }); // Filtering `null` fields;

        unset($data[self::FIELD_MERCHANT_SIG]);
        ksort($data, SORT_STRING);

        return $this->SHA256signature($hmac_key, $data);
    }

    private function calculateSHA1(string $hmac_key): string
    {
        return $this->SHA1signature($hmac_key, [
            $this->getAuthResult(),
            $this->getPspReference(),
            $this->getMerchantReference(),
            $this->getSkinCode(),
            $this->getMerchantReturnData()
        ]);
    }

    /**
     * @param string $hmac_key HMAC key
     * @return bool
     * @throws \RuntimeException
     */
    public function checkSignatureValidity(string $hmac_key): bool
    {
        if ($this->calculateMerchantSig($hmac_key) !== $this->getMerchantSig()) {
            throw new \RuntimeException('Merchant signature didn\'t match', 100);
        }

        return true;
    }
}
