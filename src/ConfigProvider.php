<?php

declare(strict_types=1);

namespace Olobase\Mezzio;

use Mezzio\Application;
use Psr\Container\ContainerInterface;
use Olobase\Mezzio\Authentication\Contracts\RoleModelInterface;
use Olobase\Mezzio\Authentication\Contracts\PermissionModelInterface;
use Olobase\Mezzio\Authorization\Model\NullRoleModel;
use Olobase\Mezzio\Authorization\Model\NullPermissionModel;
use Olobase\Mezzio\Router\AttributeRouteCollector;
use Olobase\Mezzio\Router\AttributeRouteProviderInterface;

/**
 * @see ConfigInterface
 */
class ConfigProvider
{
    /**
     * Return oloma-dev configuration.
     *
     * @return array
     */
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
        ];
    }

    /**
     * Return application-level dependency configuration.
     *
     * @return ServiceManagerConfigurationType
     */
    public function getDependencyConfig()
    {
        return [
            'factories' => [
                Error\ErrorWrapperInterface::class => Error\ErrorWrapperFactory::class,
                DataTable\ColumnFiltersInterface::class => DataTable\ColumnFiltersFactory::class,

                AttributeRouteProviderInterface::class => function (ContainerInterface $container) {
                    return new AttributeRouteCollector(
                        $container->get(Application::class),
                        $container
                    );
                },

                PermissionModelInterface::class => function ($container) {
                    if ($container->has(\Authorization\Model\PermissionModel::class)) {
                        return $container->has(\Authorization\Model\PermissionModel::class);
                    }
                    return new NullPermissionModel();
                },
                
                RoleModelInterface::class => function ($container) {
                    if ($container->has(\Authorization\Model\RoleModel::class)) {
                        return $container->get(\Authorization\Model\RoleModel::class);
                    }
                    return new NullRoleModel();
                },

            ],
        ];
    }
}
