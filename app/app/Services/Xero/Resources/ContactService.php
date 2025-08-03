<?php

namespace App\Services\Xero\Resources;

use App\Services\Xero\TokenManager;
use App\Services\Xero\XeroClient;
use Throwable;


class ContactService
{
    /**
     * @var TokenManager
     */
    protected TokenManager $tokens;

    /**
     * ContactService constructor.
     * @param TokenManager $tokens
     */
    public function __construct(TokenManager $tokens)
    {
        $this->tokens = $tokens;
    }

    /**
     * Fetch a list of contacts from Xero for the given user.
     * Uses the TokenManager to get a valid token for the user.
     *
     * @param \App\Models\User $user The user making the request
     * @param int $limit The maximum number of contacts to return
     * @return array An array of XeroAPI\XeroPHP\Models\Accounting\Contact
     */
    public function get($user, $limit = 5): array
    {
        $token = $this->tokens->getValidTokenFor($user);
        if (!$token) {
            return [];
        }
        $client = new XeroClient($token->access_token);
        $api = $client->accounting;

        try {
            // Fetch contacts from Xero API
            $response = $api->getContacts(
                $token->tenant_id,     // tenant ID from token
                null,                  // ifModifiedSince (optional filter)
                null,                  // where clause (e.g., filtering by status)
                'UpdatedDateUTC DESC', // order by
                null,                  // IDs (optional specific contacts)
                null,                  // contactNumbers
                1,                     // page number
                null,                  // includeArchived
                null,                  // summaryOnly
                null,                  // searchTerm
                5,                     // pageSize
            );

            // Return up to $limit contacts
            return array_slice($response?->getContacts() ?? [], 0, $limit);
        } catch (Throwable $e) {
            // Log any errors encountered during API call
            logger()->warning("Xero: Failed to fetch contacts", [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }
}
