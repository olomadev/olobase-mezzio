<?php

declare(strict_types=1);

namespace Oloma\Php\Container;

use Oloma\Php\DataManager;
use Psr\Container\ContainerInterface;

class DataManagerFactory
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new DataManager;
    }
}
