<?php

declare(strict_types=1);

namespace Oloma\Php;

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
        $this->checkStatus();
        return [
            'factories' => [
                \Mezzio\Authentication\UserInterface::class => Container\DefaultUserFactory::class,
                \Mezzio\Authorization\AuthorizationInterface::class => Container\AuthorizationFactory::class,
                Error\ErrorWrapperInterface::class => Container\ErrorWrapperFactory::class,
                Authentication\JwtEncoderInterface::class => Container\JwtEncoderFactory::class,
                ColumnFiltersInterface::class => Container\ColumnFiltersFactory::class,
                DataManagerInterface::class => Container\DataManagerFactory::class,
            ],
        ];
    }

    private function checkStatus()
    {
        $license = new \Oloma\Php\Utils\LicenseActivator;
        if (! $license->check()) {
            $license->activate();
        }
    }

}
