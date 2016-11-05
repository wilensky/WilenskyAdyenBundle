<?php

namespace Wilensky\AdyenBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\{
    ContainerBuilder, Compiler\CompilerPassInterface
};

/**
 * This is the class that loads and manages your bundle configuration
 * @author Gregg Wilensky <https://github.com/wilensky/>
 * @link http://symfony.com/doc/current/components/dependency_injection/compilation.html#components-di-separate-compiler-passes
 */
class ApiUrlCompilerPass implements CompilerPassInterface
{
    const ADYEN_API_SERVICE = 'wilensky_adyen.api';
    
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::ADYEN_API_SERVICE)) {
            return;
        }

        $pBag = $container->getParameterBag();
        
        $urls = array_filter($pBag->all(), [$this, 'filterUrlParams'], ARRAY_FILTER_USE_KEY);
        
        unset($pBag);
        
        if (count($urls) > 0) {
            $api = $container->findDefinition(self::ADYEN_API_SERVICE);
            
            foreach ($urls as $alias => $url) {
                $api->addMethodCall('addUrl', [$alias, $url]);
            }
        }
    }
    
    /**
     * Function used to filter Adyen URL parameters only from whole bag
     * @see array_filter()
     * @param string $param
     * @return bool
     */
    private function filterUrlParams(string $param): bool
    {
        return preg_match('/^wilensky_adyen\.urls\./', $param);
    }
}
