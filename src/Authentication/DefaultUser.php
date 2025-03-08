<?php

declare(strict_types=1);

namespace Olobase\Mezzio\Authentication;

use Mezzio\Authentication\UserInterface;

/**
 * Default implementation of UserInterface.
 *
 * This implementation is modeled as immutable, to prevent propagation of
 * user state changes.
 *
 * We recommend that any details injected are serializable.
 */
final class DefaultUser implements UserInterface
{
    /**
     * User id
     * @var string
     */
    private $id;

    /**
     * User email
     * @var string
     */
    private $identity;

    /**
     * User roles
     * @var string[]
     */
    private $roles;

    /**
     * User details
     * @var array
     */
    private $details;

    /**
     * Constuctor
     *
     * @param string $id        user_id
     * @param string $identity  user email
     * @param array  $roles     user roles for frontend
     * @param array  $details   extra details
     */
    public function __construct(
        string $id, 
        string $identity, 
        array $roles = [], 
        array $details = []
    )
    {
        $this->id = $id;
        $this->identity = $identity;
        $this->roles = $roles;
        $this->details= $details;
    }

    public function getId() : string
    {
        return $this->id;
    }

    public function getIdentity() : string
    {
        return $this->identity;
    }

    public function getRoles() : iterable
    {
        return $this->roles;
    }

    public function getDetails() : array
    {
        return $this->details;
    }
    
    public function getDetail(string $name, $default = null)
    {
        return isset($this->details[$name]) ? $this->details[$name] : $default;
    }
}
