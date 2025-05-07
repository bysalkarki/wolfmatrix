<?php
namespace App\Services;

use Illuminate\Support\Facades\Cache;

class LoginAttemptService
{
    protected $maxAttempts = 3;
    protected $baseDelay = 60;

    public function recordFailedAttempt($email)
    {
        $key = "login_attempts:{$email}";
        $attempts = Cache::get($key, 0) + 1;
        Cache::put($key, $attempts, now()->addMinutes(15));

        return $attempts;
    }

    public function getDelay($email)
    {
        $attempts = Cache::get("login_attempts:{$email}", 0);
        if ($attempts >= $this->maxAttempts) {
            return $this->baseDelay * pow(2, $attempts - $this->maxAttempts);
        }
        return 0;
    }

    public function clearAttempts($email)
    {
        Cache::forget("login_attempts:{$email}");
    }
}
