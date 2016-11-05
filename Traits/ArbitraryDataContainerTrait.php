<?php
declare(strict_types = 1);

namespace Wilensky\AdyenBundle\Traits;

/**
 * @author Gregg Wilensky <https://github.com/wilensky/>
 */
trait ArbitraryDataContainerTrait
{
    /**
     * Arbitrary data container
     * @var array
     */
    private $arbitraryData = [];
    
    /**
     * @param array $data
     * @return $this
     */
    public function setData(array $data = [])
    {
        $this->arbitraryData = $data;
        return $this;
    }
    
    /**
     * @param string $k
     * @param mixed|null $v
     * @return $this
     */
    public function addData(string $k, $v = null)
    {
        $this->arbitraryData[$k] = $v;
        return $this;
    }
    
    /**
     * @param string $k A data key to retrieve
     * @return array|mixed
     */
    public function getData(string $k = null)
    {
        return $k === null ? $this->arbitraryData : ($this->arbitraryData[$k] ?? null) ;
    }
    
    /**
     * @param string $k
     * @return bool
     */
    public function hasData(string $k): bool
    {
        return isset($this->arbitraryData[$k]);
    }
    
    /**
     * @param string $json
     * @return $this
     */
    public function setJsonData(string $json)
    {
        $this->arbitraryData = json_decode($json, true);
        return $this;
    }
}
