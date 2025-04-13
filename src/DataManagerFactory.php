<?php

declare(strict_types=1);

namespace Olobase\Mezzio;

use Olobase\Mezzio\DataManager;
use Psr\Container\ContainerInterface;

class DataManagerFactory
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new DataManager($container->get('config'));
    }
}
