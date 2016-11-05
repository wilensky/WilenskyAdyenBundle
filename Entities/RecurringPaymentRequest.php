<?php
declare(strict_types = 1);

namespace Wilensky\AdyenBundle\Entities;

/**
 * Adyen recurring payment data request entity
 * @author Gregg Wilensky <https://github.com/wilensky/>
 */
class RecurringPaymentRequest extends AbstractEntity
{
    /**
     * No contract
     */
    const CONTRACT_NONE = null;

    /**
     * Payment details are stored for future use.
     * For cards, the security code (CVC/CVV) is not required for subsequent payments.
     */
    const CONTRACT_RECURRING = 'RECURRING';

    /**
     * The shopper opts in to storing their card details for future use.
     * The shopper is present for the subsequent transaction, for cards the security code (CVC/CVV) is required.
     */
    const CONTRACT_ONECLICK = 'ONECLICK';

    /**
     * Payment details are stored for future use.
     * This allows the use of the stored payment details regardless of whether the shopper is on your site or not.
     */
    const CONTRACT_ONECLICK_RECURRING = 'RECURRING,ONECLICK';

    /**
     * Shopper interaction type
     */
    const SI_CONT_AUTH = 'ContAuth';

    /**
     * Recurring detail reference
     */
    const LATEST = 'LATEST';

    const FIELD_MERCHANT_ACCOUNT = 'merchantAccount';
    const FIELD_AMOUNT = 'amount';
    const FIELD_REFERENCE = 'reference';
    const FIELD_SHOPPER_EMAIL = 'shopperEmail';
    const FIELD_SHOPPER_IP = 'shopperIP';
    const FIELD_SHOPPER_REFERENCE = 'shopperReference';
    const FIELD_SHOPPER_INTERACTION = 'shopperInteraction';
    const FIELD_SELECTED_RECURRING_DETAIL_REFERENCE = 'selectedRecurringDetailReference';
    const FIELD_RECURRING = 'recurring';

    /**
     * The merchant account for which you want to process the payment.
     * @param string $v
     * @return RecurringPaymentRequest
     */
    public function setMerchantAccount(string $v)
    {
        return $this->addData(self::FIELD_MERCHANT_ACCOUNT, $v);
    }

    /**
     * The amount to authorize.
     * This consists of a currencyCode and a paymentAmount.
     * Please refer to section 2.2 of the Adyen HPP API Manual for more information.
     * @param float $amount
     * @param float $currency
     * @return RecurringPaymentRequest
     */
    public function setAmount(float $amount, string $currency)
    {
        return $this->addData(self::FIELD_AMOUNT, [
            'value' => (int)($amount * 100),
            'currency' => $currency
        ]);
    }

    /**
     * @param string $v
     * @return type
     */
    public function setReference(string $v)
    {
        return $this->addData(self::FIELD_REFERENCE, $v);
    }

    /**
     * The shopper's email address.
     * This does not have to match the email address supplied with the initial payment.
     * @param string $v
     * @return RecurringPaymentRequest
     * @throws EEE
     */
    public function setShopperEmail(string $v)
    {
        if (!$v) {
            throw new \Exception('Shopper email can\'t be empty', 100);
        }

        return $this->addData(self::FIELD_SHOPPER_EMAIL, $v);
    }

    /**
     * @param string $v
     * @return type
     */
    public function setShopperIp(string $v)
    {
        return $this->addData(self::FIELD_SHOPPER_IP, $v);
    }

    /**
     * An ID that uniquely identifies the shopper.
     * This shopperReference MUST be the same as the shopperReference used in the initial payment.
     * @param string $v
     * @return RecurringPaymentRequest
     */
    public function setShopperReference(string $v)
    {
        return $this->addData(self::FIELD_SHOPPER_REFERENCE, $v);
    }

    /**
     * The recurringDetailReference you want to use for this payment.
     * The value `LATEST` can be used to select the most recently stored recurring detail.
     * @param string $v
     * @return RecurringPaymentRequest
     */
    public function setSelectedRecurringDetailReference(string $v = self::LATEST)
    {
        return $this->addData(self::FIELD_SELECTED_RECURRING_DETAIL_REFERENCE, $v);
    }

    /**
     * This should be the same value that was submitted using recurringContract in the payment
     *  where the recurring contract was created.
     * However, if “ONECLICK,RECURRING” was specified initially, then this field should be “RECURRING”.
     * Please refer to section 3 for more information.
     * @param string $v
     * @return RecurringPaymentRequest
     */
    public function setRecurring(string $v = self::CONTRACT_RECURRING)
    {
        return $this->addData(self::FIELD_RECURRING, ['contract' => $v]);
    }

    /**
     * Set to `ContAuth`
     * @param string $v
     * @return RecurringPaymentRequest
     */
    public function setShopperInteraction(string $v = self::SI_CONT_AUTH)
    {
        return $this->addData(self::FIELD_SHOPPER_INTERACTION, $v);
    }
}
