<?php

namespace App\Services\Xero\Resources;

use App\Services\Xero\XeroClient;
use Throwable;
use XeroAPI\XeroPHP\Models\Accounting\Account;

class BankAccountService
{
    public function get($user, $token): array
    {
        $client = new XeroClient($token->access_token);
        $api = $client->accounting;

        $accounts = [];
        try {
            $accounts = $api->getAccounts(
                $token->tenant_id,
                null,
                'Type=="' . Account::BANK_ACCOUNT_TYPE_BANK . '"'
            )?->getAccounts() ?? [];
        } catch (Throwable $e) {
            logger()->warning("Xero: Failed to fetch bank accounts", ['error' => $e->getMessage()]);
        }

        $balances = $this->getBankBalances($api, $token->tenant_id);

        // Attach balances into Account models (extend with ->balance prop manually)
        foreach ($accounts as $account) {
            $id = $account->getAccountID();
            $account->balance = $balances[$id] ?? null;
        }

        return $accounts;
    }

    protected function getBankBalances($api, $tenantId): array
    {
        $balances = [];

        try {
            $report = $api->getReportBankSummary($tenantId);
            $rows = $report->getReports()[0]?->getRows() ?? [];

            foreach ($rows as $row) {
                if ($row->getRowType() === 'Section') {
                    foreach ($row->getRows() as $subRow) {
                        if ($subRow->getRowType() === 'Row') {
                            $cells = $subRow->getCells();
                            $attrs = $cells[0]?->getAttributes() ?? [];

                            $accountId = collect($attrs)
                                ->firstWhere(fn($attr) => strtolower($attr->getId()) === 'accountid')
                                ?->getValue();

                            $closingBalance = (float) ($cells[4]?->getValue() ?? 0);

                            if ($accountId) {
                                $balances[$accountId] = $closingBalance;
                            }
                        }
                    }
                }
            }
        } catch (Throwable $e) {
            logger()->warning("Xero: Failed to fetch bank balances", ['error' => $e->getMessage()]);
        }

        return $balances;
    }
}
