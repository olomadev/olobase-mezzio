<?php

declare(strict_types=1);

namespace Oloma\Php\Authentication;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * @author Oloma <support@oloma.dev>
 *
 * Column filters
 */
final class JwtEncoder implements JwtEncoderInterface
{
    private $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function encode(array $payload): string
    {
        return JWT::encode($payload, $this->key, 'HS256');
    }

    public function decode(string $token): array
    {
        JWT::$leeway = 60;
        $decoded = JWT::decode($token, new Key($this->key, 'HS256'));
        return (array)$decoded;
    }
}
