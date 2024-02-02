<?php

declare(strict_types=1);

namespace Olobase\Mezzio\Authentication;

use Psr\Container\ContainerInterface;
use Olobase\Mezzio\Authentication\JwtEncoder;
use Laminas\ServiceManager\Factory\FactoryInterface;

class JwtEncoderFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        return new JwtEncoder($config['token']);
    }
}
