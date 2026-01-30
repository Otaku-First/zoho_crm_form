<?php

namespace App\Http\Controllers;

use App\Data\CreateAccountData;
use App\Data\CreateDealData;
use App\Services\ZohoCrmService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ZohoCrmFormController extends Controller
{
    public function __construct(
        private readonly ZohoCrmService $zohoCrmService,
    ) {}

    public function index(): Response
    {
        return Inertia::render('CrmForm');
    }

    public function createAccount(CreateAccountData $data, Request $request)
    {
        $sessionId = $request->session()->getId();

        $response = $this->zohoCrmService->createAccount($data, $sessionId);
        $accountId = $response['data'][0]['details']['id'];

        return back()->with('success', 'Account created successfully!')->with('accountId', $accountId);
    }

    public function createDeal(CreateDealData $data, Request $request)
    {
        $sessionId = $request->session()->getId();
        $accountId = $request->input('account_id');

        $this->zohoCrmService->createDeal($data, $accountId, $sessionId);

        return redirect('/')->with('success', 'Account and Deal created successfully in Zoho CRM!');
    }
}
