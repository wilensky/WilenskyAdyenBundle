<?php
declare(strict_types = 1);

namespace Wilensky\AdyenBundle\Entities;

use Wilensky\AdyenBundle\Entities\RecurringDetail as RDItem;

/**
 * @author Gregg Wilensky <https://github.com/wilensky/>
 */
class RecurringDetailsResponse extends AbstractEntity
{
    const FIELD_CREATION_DATE = 'creationDate';
    const FIELD_SHOPPER_REFERENCE = 'shopperReference';
    const FIELD_LAST_KNOWN_SHOPPER_EMAIL = 'lastKnownShopperEmail';
    const FIELD_DETAILS = 'details';

    const RECURRING_DETAIL_KEY = 'RecurringDetail';

    public function getCreationDate(): \DateTime
    {
        return \DateTime::createFromFormat(\DateTime::ISO8601, $this->getData(self::FIELD_CREATION_DATE));
    }

    public function getShopperReference(): string
    {
        return $this->getData(self::FIELD_SHOPPER_REFERENCE);
    }

    public function getLastKnownShopperEmail(): string
    {
        return $this->getData(self::FIELD_LAST_KNOWN_SHOPPER_EMAIL);
    }

    public function getDetails(): array
    {
        return $this->getData(self::FIELD_DETAILS) ?? [];
    }

    public function getRecurringDetails(): array
    {
        return array_map(function (array $rdi):RDItem {
            return new RDItem($rdi[self::RECURRING_DETAIL_KEY] ?? []);
        }, $this->getDetails());
    }
}
