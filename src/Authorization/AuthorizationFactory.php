<?php

declare(strict_types=1);

namespace Olobase\Mezzio\Authorization;

use Olobase\Mezzio\Authorization\Authorization;
use Olobase\Mezzio\Authorization\PermissionModelInterface;
use Psr\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class AuthorizationFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new Authorization($container->get(PermissionModelInterface::class));
    }
}
