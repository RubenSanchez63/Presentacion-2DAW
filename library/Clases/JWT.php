<?php
namespace Clases;

/**
 * Clase JWT simplificada (HS256) sin dependencias externas
 */
class JWT
{
    private static string $secret = 'biblioteca_secret_key_2024'; // You can change this to a more secure key in production

    public static function generate(array $payload): string
    {
        $header  = self::base64url(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $payload = self::base64url(json_encode($payload));
        $sig     = self::base64url(hash_hmac('sha256', "$header.$payload", self::$secret, true));
        return "$header.$payload.$sig";
    }

    public static function verify(string $token): array|false
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) return false;

        [$header, $payload, $sig] = $parts;
        $expected = self::base64url(hash_hmac('sha256', "$header.$payload", self::$secret, true));
        if (!hash_equals($expected, $sig)) return false;

        $data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
        if (isset($data['exp']) && $data['exp'] < time()) return false;

        return $data;
    }

    private static function base64url(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
