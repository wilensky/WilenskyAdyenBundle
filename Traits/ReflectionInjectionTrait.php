<?php
declare(strict_types = 1);

namespace Wilensky\AdyenBundle\Traits;

/**
 * @author Gregg Wilensky <https://github.com/wilensky/>
 */
trait ReflectionInjectionTrait
{
    /**
     * @var \ReflectionClass
     */
    private $reflection;

    /**
     * @return \ReflectionClass
     */
    private function getReflection(): \ReflectionClass
    {
        if (!($this->reflection instanceof \ReflectionClass)) {
            $this->reflection = new \ReflectionClass(get_called_class());
        }

        return $this->reflection;
    }

    /**
     * @param string|null $filterPrefix
     * @return array
     */
    public function getConstantsValue(string $filterPrefix = null): array
    {
        $constants = $this->getReflection()->getConstants();
        $cc = get_called_class();

        if (null !== $filterPrefix) {
            $constants = array_filter(array_map(function ($const) use ($filterPrefix) {
                return 0 === strpos($const, $filterPrefix) ? $const : null;
            }, array_keys($constants)));
        }

        return array_map(function ($const) use ($cc) {
            return constant($cc . '::' . $const);
        }, $constants);
    }
}
