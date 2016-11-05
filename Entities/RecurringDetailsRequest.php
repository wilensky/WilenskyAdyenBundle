<?php
declare(strict_types = 1);

namespace Wilensky\AdyenBundle\Entities;

/**
 * @author Gregg Wilensky <https://github.com/wilensky/>
 */
class RecurringDetailsRequest extends AbstractEntity
{
    const FIELD_SHOPPER_REFERENCE = 'shopperReference';
    const FIELD_MERCHANT_ACCOUNT = 'merchantAccount';
    const FIELD_RECURRING = 'recurring';

    /**
     * Payment details are stored for future use.
     * For cards, the security code (CVC/CVV) is not required for subsequent payments.
     */
    const CONTRACT_RECURRING = 'RECURRING';

    public function setShopperReference(string $v)
    {
        return $this->addData(self::FIELD_SHOPPER_REFERENCE, $v);
    }

    public function setMerchantAccount(string $v)
    {
        return $this->addData(self::FIELD_MERCHANT_ACCOUNT, $v);
    }

    public function setRecurring(string $v = self::CONTRACT_RECURRING)
    {
        return $this->addData(self::FIELD_RECURRING, ['contract' => $v]);
    }
}
