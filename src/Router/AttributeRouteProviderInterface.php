<?php

namespace Olobase\Mezzio\Router;

interface AttributeRouteProviderInterface
{
    public function registerRoutes(string $moduleDirectory): void;
}