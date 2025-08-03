<?php

namespace App\Services\Xero\Resources;

use App\Services\Xero\TokenManager;
use App\Services\Xero\XeroClient;
use Psr\Log\LoggerInterface;
use Throwable;
use XeroAPI\XeroPHP\Models\Accounting\Invoice;


class InvoiceService
{
    /**
     * @var TokenManager
     */
    protected TokenManager $tokens;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * InvoiceService constructor.
     * @param TokenManager $tokens
     */
    public function __construct(TokenManager $tokens, LoggerInterface $logger)
    {
        $this->tokens = $tokens;
        $this->logger = $logger;
    }

    /**
     * Fetch a list of invoices from Xero for the given user.
     * Uses the TokenManager to get a valid token for the user.
     *
     * @param \App\Models\User $user The user making the request
     * @param int $limit The maximum number of invoices to return
     * @return array An array of XeroAPI\XeroPHP\Models\Accounting\Invoice
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
            $response = $api->getInvoices(
                $token->tenant_id,
                null,                 // ifModifiedSince
                null,                 // where
                'Date DESC',          // order
                null,                 // iDs
                null,                 // invoiceNumbers
                null,                 // contactIDs
                [Invoice::STATUS_AUTHORISED, Invoice::STATUS_DRAFT, Invoice::STATUS_PAID, Invoice::STATUS_SUBMITTED],
                1,                    // page
                false,                // includeArchived
                false,                // createdByMyApp
                null,                 // unitdp
                false,                // summaryOnly
                5,                    // pageSize
                null                  // searchTerm
            );
            return array_slice($response?->getInvoices() ?? [], 0, $limit);
        } catch (Throwable $e) {
            $this->logger->warning("Xero: Failed to fetch invoices", ['error' => $e->getMessage()]);
            return [];
        }
    }
}
