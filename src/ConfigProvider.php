<?php

declare(strict_types=1);

namespace Olobase\Mezzio;

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
                \Mezzio\Authentication\UserInterface::class => Authentication\DefaultUserFactory::class,
                \Mezzio\Authorization\AuthorizationInterface::class => Authorization\AuthorizationFactory::class,
                Error\ErrorWrapperInterface::class => Error\ErrorWrapperFactory::class,
                Authentication\JwtEncoderInterface::class => Authentication\JwtEncoderFactory::class,
                ColumnFiltersInterface::class => ColumnFiltersFactory::class,
                DataManagerInterface::class => DataManagerFactory::class,
            ],
        ];
    }
}
