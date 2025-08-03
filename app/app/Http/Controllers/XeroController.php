<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Http;
use App\Services\Xero\Resources\OrganisationService;
use App\Models\XeroToken;

class XeroController extends Controller
{
    public function redirectToXero()
    {
        $query = http_build_query([
            'response_type' => 'code',
            'client_id' => env('XERO_CLIENT_ID'),
            'redirect_uri' => env('XERO_REDIRECT_URI'),
            'scope' => env('XERO_SCOPES'),
            'state' => csrf_token(),
        ]);

        return redirect("https://login.xero.com/identity/connect/authorize?$query");
    }

    public function handleCallback(Request $request)
    {
        $response = Http::asForm()->post('https://identity.xero.com/connect/token', [
            'grant_type' => 'authorization_code',
            'code' => $request->input('code'),
            'redirect_uri' => env('XERO_REDIRECT_URI'),
            'client_id' => env('XERO_CLIENT_ID'),
            'client_secret' => env('XERO_CLIENT_SECRET'),
        ]);

        $data = $response->json();

        $tenantRes = Http::withToken($data['access_token'])
            ->get('https://api.xero.com/connections');

        $tenantId = $tenantRes->json()[0]['tenantId'] ?? null;

        XeroToken::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'access_token' => $data['access_token'],
                'refresh_token' => $data['refresh_token'],
                'tenant_id' => $tenantId,
                'expires_in' => $data['expires_in'],
                'fetched_at' => now(),
            ]
        );

        return redirect('/dashboard');
    }

    public function dashboard(OrganisationService $service)
    {
        $org = $service->get(auth()->user());

        $org = $org ? [
            'name' => $org->getName(),
            'country' => $org->getCountryCode(),
        ] : null;

        return Inertia::render('dashboard', [
            'org' => $org
        ]);
    }
}
