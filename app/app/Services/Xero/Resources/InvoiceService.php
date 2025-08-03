<?php

namespace App\Services\Xero\Resources;

use App\Services\Xero\XeroClient;
use Throwable;
use XeroAPI\XeroPHP\Models\Accounting\Invoice;

class InvoiceService
{
    public function get($user, $token, $limit = 5)
    {
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
                [Invoice::STATUS_AUTHORISED, Invoice::STATUS_DRAFT, Invoice::STATUS_PAID, INVOICE::STATUS_SUBMITTED], // statuses — ← this is often required
                1,                    // page
                false,                 // includeArchived
                false,                // createdByMyApp
                null,                    // unitdp
                false,                // summaryOnly
                5,                   // pageSize
                null                  // searchTermu
            );
            return array_slice($response?->getInvoices() ?? [], 0, $limit);
        } catch (Throwable $e) {
            logger()->warning("Xero: Failed to fetch invoices", ['error' => $e->getMessage()]);
            return [];
        }
    }
}
