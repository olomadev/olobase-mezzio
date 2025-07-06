<?php

declare(strict_types=1);

namespace Olobase\Mezzio\Authentication\Service;

use Psr\Http\Message\ServerRequestInterface;
use Olobase\Mezzio\Authentication\Helper\TokenEncryptHelper;

interface TokenModelInterface
{
    /**
     * Decode token
     * 
     * @param  string $token token
     * @return mixed
     */
    public function decode(string $token);
    
    /**
     * Creates a new token for the given request
     * 
     * @param  ServerRequestInterface $request request
     * @return array|boolean
     */
    public function create(ServerRequestInterface $request);

    /**
     * Refresh token
     * 
     * @param  ServerRequestInterface $request request
     * @param  array                  $decoded payload
     * @return array|boolean
     */
    public function refresh(ServerRequestInterface $request, array $decoded);

    /**
     * Kill current token for logout operation
     * 
     * @param  string $userId  user id
     * @param  string $tokenId token id
     * @return void
     */
    public function kill(string $userId, string $tokenId);

    /**
     * Returns the token encryption helper object
     * 
     * @return TokenEncryptHelper
     */
    public function getTokenEncrypt(): TokenEncryptHelper;
}
