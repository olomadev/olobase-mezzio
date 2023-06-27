<?php

declare(strict_types=1);

namespace Oloma\Php\Authentication;

/**
 * @author Oloma <support@oloma.dev>
 *
 * Jwt encode interface
 */
interface JwtEncoderInterface
{
    /**
     * Encode array data to jwt token string
     * 
     * @param  array  $paylod 
     * @return string
     */
    public function encode(array $paylod) : string;

    /**
     * Decode token as array
     * 
     * @param  string $token 
     * @return array
     */
    public function decode(string $token) : array;
}