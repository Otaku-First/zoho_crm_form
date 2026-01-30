<?php

namespace App\Http\Middleware;

use App\Models\ZohoToken;
use App\Services\ZohoAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $token = ZohoToken::forSession($request->session()->getId())->latest()->first();

        if ($token && $token->isExpired() && $token->refresh_token) {
            try {
                app(ZohoAuthService::class)->refreshToken($token);
                $token->refresh();
            } catch (\Throwable $e) {
                Log::warning('Zoho token refresh in middleware failed', ['error' => $e->getMessage()]);
            }
        }

        return [
            ...parent::share($request),
            'zohoConnected' => $token && !$token->isExpired(),
            'expiresAt' => $token?->expires_at?->getTimestamp(),
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
                'accountId' => $request->session()->get('accountId'),
            ],
        ];
    }
}
