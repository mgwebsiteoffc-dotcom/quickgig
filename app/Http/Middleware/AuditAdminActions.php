<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Records every state-changing action taken inside /admin.
 *
 * Reads (GET/HEAD) are ignored — this is an accountability trail, not analytics.
 * Secrets are never written: anything that looks like a key, password or token
 * is replaced before the row is saved.
 */
class AuditAdminActions
{
    private const SENSITIVE = ['password', 'password_confirmation', 'secret', 'key', 'token', 'api_key', 'webhook'];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'], true)) {
            return $response;
        }

        try {
            $user = $request->user();

            AuditLog::create([
                'user_id' => $user?->id,
                'name'    => $user?->name,
                'role'    => $user?->role,
                'method'  => $request->method(),
                'route'   => $request->route()?->getName(),
                'path'    => '/' . ltrim($request->path(), '/'),
                'payload' => $this->scrub($request->except(['_token', '_method'])),
                'status'  => $response->getStatusCode(),
                'ip'      => $request->ip(),
            ]);
        } catch (\Throwable $e) {
            // An audit failure must never break an admin action.
            Log::warning('audit.failed', ['error' => $e->getMessage()]);
        }

        return $response;
    }

    private function scrub(array $input): array
    {
        foreach ($input as $key => $value) {
            if (is_array($value)) {
                $input[$key] = $this->scrub($value);

                continue;
            }

            foreach (self::SENSITIVE as $needle) {
                if (str_contains(strtolower((string) $key), $needle)) {
                    $input[$key] = filled($value) ? '••••••' : null;

                    continue 2;
                }
            }

            if (is_string($value) && mb_strlen($value) > 300) {
                $input[$key] = mb_substr($value, 0, 300) . '…';
            }
        }

        return $input;
    }
}
