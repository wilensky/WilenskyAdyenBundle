<?php
declare(strict_types = 1);

namespace Wilensky\AdyenBundle\Entities;

/**
 * Adyen recurring payment data response entity
 * @author Gregg Wilensky <https://github.com/wilensky/>
 */
class RecurringPaymentResponse extends AbstractEntity
{
    const AUTHORISED = 'Authorised';
    const REFUSED = 'Refused';
    const ERROR = 'Error';

    public function getPspReference(): string
    {
        return $this->getData('pspReference');
    }

    /**
     * The result of the payment. The possible values are `Authorised`, `Refused`, `Error`
     * @return string
     */
    public function getResultCode(): string
    {
        return $this->getData('resultCode');
    }

    /**
     * An authorization code if the payment was successful. Blank otherwise.
     * @return int
     */
    public function getAuthCode(): int
    {
        $ac = (int)$this->getData('authCode');
        return $ac === 0 ? null : $ac;
    }

    /**
     * Adyen's mapped refusal reason, if the payment was refused.
     * @return string
     */
    public function getRefusalReason(): string
    {
        return $this->getData('refusalReason');
    }

    /**
     * Matches current result code with another one
     * @param string $code
     * @return boolean
     */
    private function matchResultCode($code): bool
    {
        return strtolower($this->getResultCode()) === strtolower($code);
    }

    public function isAuthorised(): bool
    {
        return $this->matchResultCode(self::AUTHORISED);
    }

    public function isRefused(): bool
    {
        return $this->matchResultCode(self::REFUSED);
    }

    public function isError(): bool
    {
        return $this->matchResultCode(self::ERROR);
    }

    public function isFailed(): bool
    {
        return $this->isRefused() || $this->isError();
    }

    public function isSuccessful(): bool
    {
        return $this->isAuthorised();
    }
}
