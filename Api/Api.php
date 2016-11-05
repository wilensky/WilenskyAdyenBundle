<?php
declare(strict_types = 1);

namespace Wilensky\AdyenBundle\Api;

use PB\PaymentBundle\Adyen\Entities\{
    RecurringPaymentRequest, RecurringPaymentResponse,
    RecurringDetailsRequest, RecurringDetailsResponse,
    DisableRecurringDetailsRequest, DisableRecurringDetailsResponse
};

use PB\PaymentBundle\Adyen\Exceptions\ApiException;

/**
 * Adyen generic API class
 * @author Gregg Wilensky <https://github.com/wilensky/>
 */
final class Api extends AbstractClient
{
    private $urls = [];
    private $merchantAccount;

    public function __construct(
        string $ma,
        string $username,
        string $password,
        int $rrt = parent::RESOURCE_READ_TIMEOUT
    ) {
        parent::__construct($username, $password, $rrt);
        $this->merchantAccount = $ma;
    }

    /**
     * @return string
     */
    protected function getMerchantAccount(): string
    {
        return $this->merchantAccount;
    }

    /**
     * Adds URL and it's alias for convenience
     * @param string $alias
     * @param string $url
     * @return $this
     * @throws \RuntimeException
     */
    public function addUrl(string $alias, string $url)
    {
        if (empty($alias)) {
            throw new \RuntimeException('Empty alias provided for URL', 100);
        } else if (empty($url)) {
            throw new \RuntimeException('Empty URL provided for alias `' . $alias . '`', 101);
        }

        $this->urls[$alias] = $url;
        return $this;
    }

    /**
     * Returns URL by alias
     * @param string $alias URL alias
     * @return string
     * @throws \RuntimeException
     */
    protected function geturl(string $alias): string
    {
        if (($this->urls[$alias] ?? null) === null) {
            throw new \RuntimeException('Inexistent URL alias requested', 110);
        }

        return $this->urls[$alias];
    }

    /**
     * @param RecurringPaymentRequest $rpr
     * @return RecurringPaymentResponse
     */
    public function createRecurringPayment(RecurringPaymentRequest $rpr)
    {
        try {
            return (new RecurringPaymentResponse())
                ->setJsonData($this->post(
                    $this->geturl('authorise'),
                    (string)$rpr->setMerchantAccount($this->getMerchantAccount())
                ));
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage());
        }
    }

    /**
     * @param RecurringDetailsRequest $rdr
     * @return RecurringDetailsResponse
     */
    public function getRecurringDetails(RecurringDetailsRequest $rdr)
    {
        return (new RecurringDetailsResponse())
            ->setJsonData($this->post(
                $this->geturl('listRecurringDetails'),
                (string)$rdr->setMerchantAccount($this->getMerchantAccount())
            ));
    }

    /**
     * Disables one or all recurring details for a single customer
     * @param DisableRecurringDetailRequest $drdr
     * @return DisableRecurringDetailResponse
     */
    public function disableRecurringDetails(DisableRecurringDetailsRequest $drdr): DisableRecurringDetailsResponse
    {
        return (new DisableRecurringDetailsResponse())
            ->setJsonData($this->post(
                $this->geturl('recurringDisable'),
                (string)$drdr->setMerchantAccount($this->getMerchantAccount())
            ));
    }
}
