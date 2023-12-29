<?php

declare(strict_types=1);

namespace Oloma\Php\Container;

use Psr\Container\ContainerInterface;
use Oloma\Php\Authentication\JwtEncoder;
use Laminas\ServiceManager\Factory\FactoryInterface;

class JwtEncoderFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        return new JwtEncoder($config['token']);
    }
}
