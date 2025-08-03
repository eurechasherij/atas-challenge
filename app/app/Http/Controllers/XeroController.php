<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Http;
use App\Services\Xero\Resources\OrganisationService;
use App\Models\XeroToken;
use App\Services\Xero\DataSyncService;
use Illuminate\Support\Facades\Auth;

class XeroController extends Controller
{
    public function redirectToXero()
    {
        $query = http_build_query([
            'response_type' => 'code',
            'client_id' => config('xero.client_id'),
            'redirect_uri' => config('xero.redirect_uri'),
            'scope' => config('xero.scopes'),
            'state' => csrf_token(),
        ]);

        return redirect("https://login.xero.com/identity/connect/authorize?$query");
    }

    public function handleCallback(Request $request)
    {
        $response = Http::asForm()->post('https://identity.xero.com/connect/token', [
            'grant_type' => 'authorization_code',
            'code' => $request->input('code'),
            'redirect_uri' => config('xero.redirect_uri'),
            'client_id' => config('xero.client_id'),
            'client_secret' => config('xero.client_secret'),
        ]);

        $data = $response->json();

        if (!isset($data['id_token'])) {
            throw new \Exception('Missing id_token from Xero response');
        }

        // Decode the JWT safely
        $segments = explode('.', $data['id_token']);
        $payload = json_decode(base64_decode(strtr($segments[1], '-_', '+/')), true);

        // Extract user data
        $email = $payload['email'] ?? null;
        $firstName = $payload['given_name'] ?? 'Xero User';
        $sub = $payload['sub'] ?? null;

        if (!$email) {
            throw new \Exception('No email found in id_token');
        }

        // Find or create Laravel user
        $user = User::firstOrCreate(
            ['email' => $email],
            ['name' => $firstName, 'password' => bcrypt(str()->random(32))]
        );

        // Login and save tokens as needed
        Auth::login($user);


        $tenantRes = Http::withToken($data['access_token'])
            ->get('https://api.xero.com/connections');
        $tenants = $tenantRes->json();
        if (count($tenants) > 1) {
            // Store tenants and tokens in session for selection
            session([
                'xero_tenants' => $tenants,
                'xero_token_data' => $data,
                'xero_user_id' => $user->id,
            ]);
            return redirect('/xero/select-tenant');
        }
        $tenantId = $tenants[0]['tenantId'] ?? null;

        XeroToken::updateOrCreate(
            ['user_id' => $user->id],
            [
                'access_token' => $data['access_token'],
                'refresh_token' => $data['refresh_token'],
                'tenant_id' => $tenantId,
                'expires_in' => $data['expires_in'],
                'fetched_at' => now(),
            ]
        );

        // Sync Xero data after login
        app(DataSyncService::class)->sync($user, $user->xeroToken);
        return redirect('/dashboard')->with('success', 'Logged in with Xero!');
    }

    public function dashboard(OrganisationService $service)
    {
        $user = Auth::user();
        $org = $service->get($user);
        $org = $org ? [
            'name' => $org->getName(),
            'country' => $org->getCountryCode(),
        ] : null;

        // Fetch latest 5 invoices, 5 contacts, all bank accounts
        $invoices = \App\Models\XeroInvoice::where('user_id', $user->id)
            ->orderByDesc('date')
            ->limit(5)
            ->get(['number', 'date', 'amount', 'status'])
            ->map(function ($inv) {
                return [
                    'number' => $inv->number,
                    'date' => $inv->date,
                    'amount' => $inv->amount,
                    'status' => $inv->status,
                ];
            });

        $contacts = \App\Models\XeroContact::where('user_id', $user->id)
            ->orderByDesc('id')
            ->limit(5)
            ->get(['name', 'email'])
            ->map(function ($c) {
                return [
                    'name' => $c->name,
                    'email' => $c->email,
                ];
            });

        $bankAccounts = \App\Models\XeroBankAccount::where('user_id', $user->id)
            ->get(['name', 'balance'])
            ->map(function ($b) {
                return [
                    'name' => $b->name,
                    'balance' => $b->balance,
                ];
            });

        return Inertia::render('dashboard', [
            'org' => $org,
            'invoices' => $invoices,
            'contacts' => $contacts,
            'bankAccounts' => $bankAccounts,
        ]);
    }


    /**
     * Fetch and persist Xero data for the authenticated user
     */
    public function syncXeroData(Request $request)
    {
        $user = Auth::user();
        $token = $user->xeroToken;
        if (!$token) {
            return redirect()->back()->with('error', 'No Xero token found.');
        }
        app(DataSyncService::class)->sync($user, $token);
        return redirect()->back()->with('success', 'Xero data synced!');
    }

    public function selectTenantPage()
    {
        $tenants = session('xero_tenants', []);
        if (empty($tenants)) {
            dd('No tenants found in session. Please log in to Xero first.');
        }
        return Inertia::render('xero/select-tenant', [
            'tenants' => $tenants,
        ]);
    }

    public function selectTenant(Request $request)
    {
        $tenantId = $request->input('tenantId');
        $userId = session('xero_user_id');
        $tokenData = session('xero_token_data');
        if (!$tenantId || !$userId || !$tokenData) {
            return redirect('/dashboard')->with('error', 'Invalid tenant selection.');
        }
        // Save the selected tenant to the user's XeroToken
        \App\Models\XeroToken::updateOrCreate(
            ['user_id' => $userId],
            [
                'access_token' => $tokenData['access_token'],
                'refresh_token' => $tokenData['refresh_token'],
                'tenant_id' => $tenantId,
                'expires_in' => $tokenData['expires_in'],
                'fetched_at' => now(),
            ]
        );
        // Clean up session
        session()->forget(['xero_tenants', 'xero_token_data', 'xero_user_id']);
        // Sync Xero data after tenant selection
        $user = \App\Models\User::find($userId);
        app(\App\Services\Xero\DataSyncService::class)->sync($user, $user->xeroToken);
        return redirect('/dashboard')->with('success', 'Tenant selected and data synced!');
    }
}
