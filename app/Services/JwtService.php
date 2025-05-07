<?php
namespace App\Services;

use Tymon\JWTAuth\Facades\JWTAuth;

class JwtService
{
    private static $instance = null;

    private function __construct() {}

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function generateToken($user)
    {
        return JWTAuth::fromUser($user);
    }

    public function refreshToken()
    {
        return JWTAuth::refresh();
    }

    public function invalidateToken()
    {
        JWTAuth::invalidate();
    }
}
