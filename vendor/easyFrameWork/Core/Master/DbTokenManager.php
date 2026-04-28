<?php
namespace vendor\easyFrameWork\Core\Master;

use DateTime;
use PDO;
use Exception;

class DbTokenManager
{
    private static PDO $db;

    /**
     * Inject PDO connection
     */
    public static function setPDO(PDO $pdo): void
    {
        self::$db = $pdo;
    }

    /**
     * Hash sécurisé du token
     */
    private static function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }
    public static function getDelegate($userId){
        return false;
    }

    /**
     * Génération d’un token API
     */
    public static function generate(string $userId, string $role, array $scopes = []): string
    {
        $token = bin2hex(random_bytes(32));
        $hash = self::hashToken($token);

        $expire = (new DateTime())->modify('+15 minutes')->format('Y-m-d H:i:s');

        $payload = [
            'role' => $role,
            'scopes' => $scopes
        ];

        $stmt = self::$db->prepare("
            INSERT INTO talariav2_security.api_key 
            (user_id, token_hash, scopes, expires_at, created_at)
            VALUES (:user_id, :hash, :scopes, :expires_at, NOW())
        ");

        $stmt->execute([
            'user_id' => $userId,
            'hash' => $hash,
            'scopes' => json_encode($payload),
            'expires_at' => $expire
        ]);

        return $token;
    }

    /**
     * Vérification d’un token
     */
    public static function verify(string $token)
    {
        $hash = self::hashToken($token);

        $stmt = self::$db->prepare("
            SELECT * FROM talariav2_security.api_key
            WHERE token_hash = :hash AND revoked = 0
            LIMIT 1
        ");

        $stmt->execute(['hash' => $hash]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) return 0;

        // Expiration
        if (new DateTime($data['expires_at']) < new DateTime()) {
            self::revoke($token);
            return -1;
        }

        // Mise à jour last_used
        self::$db->prepare("
            UPDATE talariav2_security.api_key 
            SET last_used = NOW()
            WHERE id = :id
        ")->execute(['id' => $data['id']]);

        return $data;
    }

    /**
     * Rafraîchir un token
     */
    public static function refresh(string $token): bool
    {
        $hash = self::hashToken($token);

        return self::$db->prepare("
            UPDATE talariav2_security.api_key 
            SET expires_at = DATE_ADD(NOW(), INTERVAL 15 MINUTE)
            WHERE token_hash = :hash AND revoked = 0
        ")->execute(['hash' => $hash]);
    }

    /**
     * Révoquer un token
     */
    public static function revoke(string $token): bool
    {
        $hash = self::hashToken($token);

        return self::$db->prepare("
            UPDATE talariav2_security.api_key 
            SET revoked = 1
            WHERE token_hash = :hash
        ")->execute(['hash' => $hash]);
    }

    /**
     * Vérifier un scope
     */
    public static function hasScope(array $tokenData, string $scope): bool
    {
        if (!isset($tokenData['scopes'])) return false;

        $payload = json_decode($tokenData['scopes'], true);

        if (!isset($payload['scopes'])) return false;

        return in_array($scope, $payload['scopes']);
    }

    /**
     * Récupérer rôle
     */
    public static function getRole(array $tokenData): ?string
    {
        $payload = json_decode($tokenData['scopes'], true);
        return $payload['role'] ?? null;
    }

    /**
     * Log API (optionnel mais recommandé)
     */
    public static function logRequest(
        ?int $apiKeyId,
        mixed $userId,
        string $endpoint,
        string $method,
        int $statusCode,
        int $responseTime,
        string $ip
    ): void {
        self::$db->prepare("
            INSERT INTO talariav2_security.api_log
            (api_key_id, user_id, endpoint, method, status_code, response_time_ms, ip, created_at)
            VALUES (:api_key_id, :user_id, :endpoint, :method, :status, :time, :ip, NOW())
        ")->execute([
            'api_key_id' => $apiKeyId,
            'user_id' => $userId,
            'endpoint' => $endpoint,
            'method' => $method,
            'status' => $statusCode,
            'time' => $responseTime,
            'ip' => $ip
        ]);
    }
}