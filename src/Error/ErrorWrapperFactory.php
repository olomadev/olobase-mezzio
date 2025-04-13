<?php

declare(strict_types=1);

namespace Olobase\Mezzio\Error;

use Olobase\Mezzio\Error\ErrorWrapper;
use Psr\Container\ContainerInterface;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ErrorWrapperFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new ErrorWrapper($container->get(TranslatorInterface::class));
    }
}
