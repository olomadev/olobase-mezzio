<?php

declare(strict_types=1);

namespace Oloma\Php;

use Oloma\Php\DataManager;
use Oloma\Php\ColumnFilters;
use Oloma\Php\Error\ErrorWrapperInterface as ErrorWrapper;
use Oloma\Php\Authentication\JwtEncoderInterface as JwtEncoder;

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
                ErrorWrapper::class => Container\ErrorWrapperFactory::class,
                JwtEncoder::class => Container\JwtEncoderFactory::class,
                ColumnFilters::class => Container\ColumnFiltersFactory::class,
                DataManager::class => Container\DataManagerFactory::class,
            ],
        ];
    }

}
