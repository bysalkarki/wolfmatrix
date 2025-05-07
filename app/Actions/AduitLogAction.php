<?php

namespace App\Actions;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AduitLogAction
{
    public static function logEvent(string $details): void
    {
        AuditLog::query()->create([
            "details" => $details,
            "url" => request()->path(),
            "method" => request()->method(),
            "ip" => request()->ip(),
            "agent" => request()->userAgent(),
            "user_id" => Auth::id() ?? null,
        ]);
    }
}
