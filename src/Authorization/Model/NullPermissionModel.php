<?php

declare(strict_types=1);

namespace Olobase\Mezzio\Authorization\Model;

use Laminas\Paginator\Paginator;
use Laminas\Paginator\Adapter\ArrayAdapter;
use Olobase\Mezzio\Authorization\Contracts\PermissionModelInterface;

class NullPermissionModel implements PermissionModelInterface
{
    /**
     * Find permissions
     * 
     * @return array
     */
    public function findPermissions() : array
    {

		return [
		    'admin' => [],
		    'user' => [],
		];
    }
}