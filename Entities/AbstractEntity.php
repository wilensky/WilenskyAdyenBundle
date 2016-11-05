<?php
declare(strict_types = 1);

namespace Wilensky\AdyenBundle\Entities;

use Wilensky\AdyenBundle\Traits\{
    ReflectionInjectionTrait as RIT, ArbitraryDataContainerTrait as ADCT
};

/**
 * @author Gregg Wilensky <https://github.com/wilensky/>
 */
abstract class AbstractEntity
{
    use RIT, ADCT;

    /**
     * Entity related fields
     * @var array
     */
    private $fields = [];

    /**
     * @param array $data
     * @param bool $filterFields
     */
    public function __construct(array $data = [], bool $filterFields = false)
    {
        count($data) > 0
            ? $this->setData(
                $filterFields === true ? array_intersect_key($data, array_flip($this->getFields())) : $data
            )
            : null ;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        if (count($this->fields) === 0) {
            $this->fields = $this->getConstantsValue('FIELD_');
        }

        return $this->fields;
    }

    public function __toString()
    {
        return json_encode($this->getData());
    }
}
