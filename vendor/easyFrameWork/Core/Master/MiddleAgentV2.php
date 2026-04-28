<?php
namespace Vendor\EasyFrameWork\Core\Master;

use Exception;
use vendor\easyFrameWork\Core\Master\DbTokenManager;
use vendor\easyFrameWork\Core\Master\SQLFactory;


class MiddleAgentV2
{
    private static $events = [
        "onExpire" => [],
        "onInvalid" => []
    ];

    private static $eventFile = "../include/MdlAgtEvt";

    public static function INIT()
    {
        if (file_exists(self::$eventFile)) {
            self::$events = unserialize(file_get_contents(self::$eventFile));
        }
    }

    private static function commit()
    {
        file_put_contents(self::$eventFile, serialize(self::$events));
    }

    /**
     * Attacher un événement
     */
    public static function attachEvent($event, $callback)
    {
        if (!isset(self::$events[$event])) {
            throw new Exception("Invalid event");
        }

        self::$events[$event][] = $callback;
        self::commit();
    }

    /**
     * Déclencher event
     */
    private static function trigger($event, $token)
    {
        if (!isset(self::$events[$event])) return;

        foreach (self::$events[$event] as $callback) {
            call_user_func($callback, $token);
        }
    }

    /**
     * Extraction du token Bearer
     */
    private static function getToken(): string
    {
        $headers = getallheaders();

        $authorization = $headers['Authorization'] ?? $headers['authorization'] ?? null;

        if (!$authorization || !preg_match('/Bearer (.+)/', $authorization, $matches)) {
            throw new Exception("No token provided", 401);
        }

        return $matches[1];
    }

    /**
     * Vérification token
     */
    public static function checkToken()
    {
        $sqlF=new SQLFactory(null,"../include/config.ini");
        DbTokenManager::setPDO($sqlF->getPdo());
        $token = self::getToken();

        $start = microtime(true);

        $data = DbTokenManager::verify($token);

        if ($data === 0) {
            self::trigger("onInvalid", $token);
            throw new Exception("Invalid token", 401);
        }

        if ($data === -1) {
            self::trigger("onExpire", $token);
            throw new Exception("Token expired", 401);
        }

        // LOG automatique
        self::log($data, 200, $start);

        return $data;
    }

    /**
     * Vérification rôle
     */
    public static function checkRole($requiredRole)
    {
         $sqlF=new SQLFactory(null,"../include/config.ini");
        DbTokenManager::setPDO($sqlF->getPdo());
        $data = self::checkToken();

        $role = DbTokenManager::getRole($data);

        if (is_array($requiredRole)) {
            if (!in_array($role, $requiredRole)) {
                throw new Exception("Insufficient permissions", 403);
            }
        } else {
            if ($role !== $requiredRole) {
                throw new Exception("Insufficient permissions", 403);
            }
        }

        return $data;
    }

    /**
     * Vérification scope
     */
    public static function checkScope($requiredScope)
    {
         $sqlF=new SQLFactory(null,"../include/config.ini");
        DbTokenManager::setPDO($sqlF->getPdo());
        $data = self::checkToken();

        if (!DbTokenManager::hasScope($data, $requiredScope)) {
            throw new Exception("Missing scope: " . $requiredScope, 403);
        }

        return $data;
    }

    /**
     * Refresh token
     */
    public static function refreshToken()
    {
        $token = self::getToken();
        return DbTokenManager::refresh($token);
    }

    /**
     * Logging automatique API
     */
    private static function log($data, $statusCode, $startTime)
    {
         $sqlF=new SQLFactory(null,"../include/config.ini","security");
        DbTokenManager::setPDO($sqlF->getPdo());
        $duration = (microtime(true) - $startTime) * 1000;

        DbTokenManager::logRequest(
            $data['id'],
            $data['user_id'],
            $_SERVER['REQUEST_URI'] ?? 'unknown',
            $_SERVER['REQUEST_METHOD'] ?? 'GET',
            $statusCode,
            (int)$duration,
            $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'
        );
    }
}