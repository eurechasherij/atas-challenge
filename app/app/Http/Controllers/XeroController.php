<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Http;
use App\Services\Xero\Resources\OrganisationService;
use App\Models\XeroToken;
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
        $tenantId = $tenantRes->json()[0]['tenantId'] ?? null;

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

        return redirect('/dashboard')->with('success', 'Logged in with Xero!');
    }

    public function dashboard(OrganisationService $service)
    {
        $org = $service->get(Auth::user());

        $org = $org ? [
            'name' => $org->getName(),
            'country' => $org->getCountryCode(),
        ] : null;

        return Inertia::render('dashboard', [
            'org' => $org
        ]);
    }
}
