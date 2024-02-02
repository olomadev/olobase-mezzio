<?php

declare(strict_types=1);

namespace Olobase\Mezzio\Authorization;

/**
 * @author Oloma <support@oloma.dev>
 *
 * Permission model interface
 */
interface PermissionModelInterface
{
    /**
     * Find permissions
     * 
     * @return array
     */
    public function findPermissions() : array;
}