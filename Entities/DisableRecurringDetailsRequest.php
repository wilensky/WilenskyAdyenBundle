<?php
declare(strict_types = 1);

namespace Wilensky\AdyenBundle\Entities;

/**
 * @author Gregg Wilensky <https://github.com/wilensky/>
 */
class DisableRecurringDetailsRequest extends AbstractEntity
{
    const FIELD_SHOPPER_REFERENCE = 'shopperReference';
    const FIELD_MERCHANT_ACCOUNT = 'merchantAccount';
    const FIELD_RECURRING_DETAIL_REFERENCE = 'recurringDetailReference';

    /**
     * Your merchant account.
     * @param string $v
     * @return $this
     */
    public function setMerchantAccount(string $v)
    {
        return $this->addData(self::FIELD_MERCHANT_ACCOUNT, $v);
    }

    /**
     * The ID that uniquely identifies the shopper.
     * This shopperReference must be the same as the shopperReference used in the initial payment.
     * @param string $v
     * @return $this
     */
    public function setShopperReference(string $v)
    {
        return $this->addData(self::FIELD_SHOPPER_REFERENCE, $v);
    }

    /**
     * The ID that uniquely identifies recurring detail.
     * This shopperReference must be the same as the shopperReference used in the initial payment.
     * @param string $v
     * @return $this
     */
    public function setRecurringDetailReference(string $v)
    {
        return !$v ? $this : $this->addData(self::FIELD_RECURRING_DETAIL_REFERENCE, $v);
    }
}
