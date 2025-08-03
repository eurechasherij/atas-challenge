<?php

namespace App\Services\Xero;

use App\Models\XeroToken;
use Illuminate\Support\Facades\Http;

class TokenManager
{
    public function getValidTokenFor($user): XeroToken |null
    {
        $token = $user->xeroToken;

        if (!$token) {
            return null;
        }

        $expiresAt = $token->fetched_at->addSeconds($token->expires_in);
        if (now()->gte($expiresAt)) {
            $response = Http::asForm()->post('https://identity.xero.com/connect/token', [
                'grant_type' => 'refresh_token',
                'refresh_token' => $token->refresh_token,
                'client_id' => config('xero.client_id'),
                'client_secret' => config('xero.client_secret'),
            ]);

            $data = $response->json();

            $token->update([
                'access_token' => $data['access_token'],
                'refresh_token' => $data['refresh_token'],
                'expires_in' => $data['expires_in'],
                'fetched_at' => now(),
            ]);
        }

        return $token;
    }
}
