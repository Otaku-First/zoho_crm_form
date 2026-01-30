<?php

namespace App\Services;

use App\Data\CreateAccountData;
use App\Data\CreateDealData;
use App\Exceptions\ZohoApiException;
use App\Exceptions\ZohoAuthenticationException;
use App\Models\ZohoToken;
use Closure;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZohoCrmService
{
    public function __construct(
        private readonly ZohoAuthService $authService,
    ) {}

    public function getAccessToken(string $sessionId): string
    {
        $token = ZohoToken::forSession($sessionId)->latest()->first();

        if (!$token) {
            throw new ZohoAuthenticationException('No Zoho token found. Please authenticate via /zoho/auth first.');
        }

        if ($token->isExpired()) {
            return $this->authService->refreshToken($token);
        }

        return $token->access_token;
    }

    public function createAccount(CreateAccountData $data, string $sessionId): array
    {
        return $this->requestWithRetry($sessionId, function (string $accessToken) use ($data) {
            return Http::withToken($accessToken)
                ->post(config('zoho.api_url') . '/crm/v2/Accounts', $data->toZohoPayload());
        }, 'Failed to create Account in Zoho CRM.');
    }

    public function createDeal(CreateDealData $data, string $accountId, string $sessionId): array
    {
        return $this->requestWithRetry($sessionId, function (string $accessToken) use ($data, $accountId) {
            return Http::withToken($accessToken)
                ->post(config('zoho.api_url') . '/crm/v2/Deals', $data->toZohoPayload($accountId));
        }, 'Failed to create Deal in Zoho CRM.');
    }

    private function requestWithRetry(string $sessionId, Closure $request, string $errorMessage): array
    {
        $accessToken = $this->getAccessToken($sessionId);
        $response = $request($accessToken);

        if ($response->status() === 401) {
            $token = ZohoToken::forSession($sessionId)->latest()->first();

            if ($token && $token->refresh_token) {
                Log::info('Zoho API returned 401, attempting token refresh');
                $accessToken = $this->authService->refreshToken($token);
                $response = $request($accessToken);
            }
        }

        if ($response->failed()) {
            Log::error('Zoho API request failed', ['response' => $response->json()]);
            throw new ZohoApiException($errorMessage);
        }

        return $response->json();
    }
}
