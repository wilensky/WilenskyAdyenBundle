<?php
declare(strict_types = 1);

namespace Wilensky\AdyenBundle\Entities;

/**
 * @author Gregg Wilensky <https://github.com/wilensky/>
 */
class RecurringDetailCard extends AbstractEntity
{
    const FIELD_EXPIRY_MONTH = 'expiryMonth';
    const FIELD_EXPIRY_YEAR = 'expiryYear';
    const FIELD_HOLDER_NAME = 'holderName';
    const FIELD_NUMBER = 'number';

    public function getExpiryMonth(): int
    {
        return (int)$this->getData(self::FIELD_EXPIRY_MONTH);
    }

    public function getExpiryYear(): int
    {
        return (int)$this->getData(self::FIELD_EXPIRY_YEAR);
    }

    public function getNumber(): int
    {
        return (int)$this->getData(self::FIELD_NUMBER);
    }

    public function getHolderName(): string
    {
        return (string)$this->getData(self::FIELD_HOLDER_NAME);
    }
}
