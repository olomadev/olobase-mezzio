<?php

declare(strict_types=1);

namespace Oloma\Mezzio\Authentication;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Oloma\Mezzio\Exception\JwtEncoderException;

/**
 * @author Oloma <support@oloma.dev>
 *
 * https://github.com/firebase/php-jwt
 * 
 * Column filters
 */
final class JwtEncoder implements JwtEncoderInterface
{
    private $publicKey;
    private $privateKey;

    public function __construct(array $config)
    {
        if (empty($config['public_key']) || empty($config['private_key'])) {
            throw new JwtEncoderException(
                "Public or private keys cannot not be empty in token configuration"
            );
        }
        $this->publicKey = $config['public_key'];
        $this->privateKey = $config['private_key'];
    }

    public function encode(array $payload): string
    {
        return JWT::encode($payload, $this->privateKey, 'EdDSA');
    }

    public function decode(string $token): array
    {
        JWT::$leeway = 60;
        $decoded = JWT::decode($token, new Key($this->publicKey, 'EdDSA'));
        return (array)$decoded;
    }
}
