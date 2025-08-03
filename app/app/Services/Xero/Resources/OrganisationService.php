<?php

namespace App\Services\Xero\Resources;

use App\Services\Xero\TokenManager;
use App\Services\Xero\XeroClient;

class OrganisationService
{
    public function __construct(
        protected TokenManager $tokens
    ) {}

    public function get($user)
    {
        $token = $this->tokens->getValidTokenFor($user);
        if (!$token) {
            return null;
        }
        $client = new XeroClient($token->access_token);

        return $client->accounting
            ->getOrganisations($token->tenant_id)
            ->getOrganisations()[0] ?? null;
    }
}
