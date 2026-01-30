<?php

namespace App\Http\Controllers;

use App\Services\ZohoAuthService;
use Illuminate\Http\Request;

class ZohoAuthController extends Controller
{
    public function __construct(
        private readonly ZohoAuthService $authService,
    ) {}

    public function redirect()
    {
        return redirect($this->authService->getAuthorizationUrl());
    }

    public function callback(Request $request)
    {
        if ($request->has('error')) {
            return redirect('/auth')->with('error', 'OAuth authorization failed: ' . $request->get('error'));
        }

        $this->authService->exchangeCode(
            $request->get('code'),
            $request->session()->getId(),
        );

        return redirect('/')->with('success', 'Zoho CRM connected successfully!');
    }
}
