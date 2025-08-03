<?php

namespace App\Services\Xero\Resources;

use App\Services\Xero\XeroClient;
use Throwable;
use XeroAPI\XeroPHP\Models\Accounting\Contact;

class ContactService
{
    /**
     * Fetch a list of contacts from Xero for the given user and token.
     *
     * @param \App\Models\User $user The user making the request
     * @param object $token The token object containing access_token and tenant_id
     * @param int $limit The maximum number of contacts to return
     * @return array An array of XeroAPI\XeroPHP\Models\Accounting\Contact
     */
    public function get($user, $token, $limit = 5): array
    {
        // Initialize Xero accounting API client
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
