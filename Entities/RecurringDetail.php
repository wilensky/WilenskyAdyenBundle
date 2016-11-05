<?php
declare(strict_types = 1);

namespace Wilensky\AdyenBundle\Entities;

use Wilensky\AdyenBundle\Entities\RecurringDetailCard as RDCard;

/**
 * @author Gregg Wilensky <https://github.com/wilensky/>
 */
class RecurringDetail extends AbstractEntity
{
    const FIELD_ADDITIONAL_DATA = 'additionalData';
    const FIELD_ALIAS = 'alias';
    const FIELD_ALIAS_TYPE = 'aliasType';
    const FIELD_CONTRACT_TYPES = 'contractTypes'; // array
    const FIELD_CARD = 'card'; // array
    const FIELD_CREATION_DATE = 'creationDate';
    const FIELD_FIRST_PSP_REFERENCE = 'firstPspReference';
    const FIELD_PAYMENT_METHOD_VARIANT = 'paymentMethodVariant';
    const FIELD_RECURRING_DETAIL_REFERENCE = 'recurringDetailReference';
    const FIELD_VARIANT = 'variant';

    const CARD_BIN_KEY = 'cardBin';

    public function getAdditionalData(): array
    {
        return $this->getData(self::FIELD_ADDITIONAL_DATA);
    }

    public function getCardBin(): int
    {
        return (int)$this->getAdditionalData()[self::CARD_BIN_KEY] ?? 0;
    }

    public function getContractTypes(): array
    {
        return $this->getData(self::FIELD_CONTRACT_TYPES);
    }

    public function getAlias(): string
    {
        return $this->getData(self::FIELD_ALIAS);
    }

    public function getAliasType(): string
    {
        return $this->getData(self::FIELD_ALIAS_TYPE);
    }

    public function getCard(): array
    {
        return $this->getData(self::FIELD_CARD);
    }

    /**
     * @see getCard()
     * @return RDCard
     */
    public function getCardObject(): RDCard
    {
        return new RDCard($this->getCard());
    }

    /**
     * @return \DateTime
     */
    public function getCreationDate(): \DateTime
    {
        return \DateTime::createFromFormat(\DateTime::ISO8601, $this->getData(self::FIELD_CREATION_DATE));
    }

    public function getFirstPspReference(): string
    {
        return $this->getData(self::FIELD_FIRST_PSP_REFERENCE);
    }

    public function getPaymentMethodVariant(): string
    {
        return $this->getData(self::FIELD_PAYMENT_METHOD_VARIANT);
    }

    public function getVariant(): string
    {
        return $this->getData(self::FIELD_VARIANT);
    }

    public function getRecurringDetailReference(): string
    {
        return $this->getData(self::FIELD_RECURRING_DETAIL_REFERENCE);
    }
}
