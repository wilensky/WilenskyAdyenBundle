<?php

namespace Wilensky\AdyenBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Wilensky\AdyenBundle\DependencyInjection\ApiUrlCompilerPass;

class WilenskyAdyenBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ApiUrlCompilerPass());
    }
}
