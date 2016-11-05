<?php
declare(strict_types = 1);

namespace Wilensky\AdyenBundle\Entities;

/**
 * @author Gregg Wilensky <https://github.com/wilensky/>
 */
class DisableRecurringDetailsResponse extends AbstractEntity
{
    const FIELD_RESPONSE = 'response';

    const SUCCESS_SINGLE = '[detail-successfully-disabled]';
    const SUCCESS_ALL = '[all-details-successfully-disabled]';

    private function getResponse(): string
    {
        return $this->getData(self::FIELD_RESPONSE);
    }

    public function isSuccessful(): bool
    {
        return
            $this->getResponse() === self::SUCCESS_SINGLE
            ||
            $this->getResponse() === self::SUCCESS_ALL;
    }
}
