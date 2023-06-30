<?php

declare(strict_types=1);

namespace Oloma\Php\Container;

use Oloma\Php\Error\ErrorWrapper;
use Psr\Container\ContainerInterface;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ErrorWrapperFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new ErrorWrapper($container->get(TranslatorInterface::class));
    }
}
