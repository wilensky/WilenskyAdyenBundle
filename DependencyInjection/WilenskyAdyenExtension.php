<?php

namespace Wilensky\AdyenBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class WilenskyAdyenExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        
        foreach ($config as $k => $v) { // 2-levels deep parameters composition for ease of access
            if (!is_array($v)) {
                $container->setParameter(sprintf('%s.%s', $this->getAlias(), $k), $v);
                continue;
            }
            
            foreach ($v as $_k => $_v) {
                $container->setParameter(sprintf('%s.%s.%s', $this->getAlias(), $k, $_k), $_v);
            }
        }
    }
}
