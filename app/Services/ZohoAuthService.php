<?php

namespace App\Services;

use App\Exceptions\ZohoAuthenticationException;
use App\Exceptions\ZohoTokenRefreshException;
use App\Models\ZohoToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZohoAuthService
{
    public function getAuthorizationUrl(): string
    {
        $query = http_build_query([
            'scope' => 'ZohoCRM.modules.ALL',
            'client_id' => config('zoho.client_id'),
            'response_type' => 'code',
            'access_type' => 'offline',
            'redirect_uri' => config('zoho.redirect_uri'),
            'prompt' => 'consent',
        ]);

        return config('zoho.accounts_url') . '/oauth/v2/auth?' . $query;
    }

    public function exchangeCode(string $code, string $sessionId): ZohoToken
    {
        $response = Http::asForm()->post(config('zoho.accounts_url') . '/oauth/v2/token', [
            'code' => $code,
            'client_id' => config('zoho.client_id'),
            'client_secret' => config('zoho.client_secret'),
            'redirect_uri' => config('zoho.redirect_uri'),
            'grant_type' => 'authorization_code',
        ]);

        if ($response->failed()) {
            Log::error('Zoho OAuth token exchange failed', ['response' => $response->json()]);
            throw new ZohoAuthenticationException('Failed to exchange authorization code.');
        }

        $data = $response->json();

        if (empty($data['access_token']) || empty($data['refresh_token'])) {
            Log::error('Zoho OAuth token response missing required fields', ['response' => $data]);
            throw new ZohoAuthenticationException('Invalid token response from Zoho.');
        }

        return ZohoToken::create([
            'access_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'],
            'expires_at' => now()->addSeconds($data['expires_in'] ?? 3600),
            'session_id' => $sessionId,
        ]);
    }

    public function refreshToken(ZohoToken $token): string
    {
        $response = Http::asForm()->post(config('zoho.accounts_url') . '/oauth/v2/token', [
            'refresh_token' => $token->refresh_token,
            'client_id' => config('zoho.client_id'),
            'client_secret' => config('zoho.client_secret'),
            'grant_type' => 'refresh_token',
        ]);

        if ($response->failed()) {
            Log::error('Zoho token refresh failed', ['response' => $response->json()]);
            throw new ZohoTokenRefreshException('Failed to refresh Zoho token.');
        }

        $data = $response->json();

        $token->update([
            'access_token' => $data['access_token'],
            'expires_at' => now()->addSeconds($data['expires_in'] ?? 3600),
        ]);

        return $data['access_token'];
    }
}
