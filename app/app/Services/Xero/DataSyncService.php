<?php

namespace App\Services\Xero;

use App\Helpers\XeroHelper;
use App\Models\XeroOrganisation;
use App\Models\XeroInvoice;
use App\Models\XeroContact;
use App\Models\XeroBankAccount;
use App\Services\Xero\Resources\OrganisationService;
use App\Services\Xero\Resources\InvoiceService;
use App\Services\Xero\Resources\ContactService;
use App\Services\Xero\Resources\BankAccountService;
use Throwable;

class DataSyncService
{
    public function __construct(
        protected OrganisationService $organisationService,
        protected InvoiceService $invoiceService,
        protected ContactService $contactService,
        protected BankAccountService $bankAccountService,
    ) {}

    public function sync($user, $token): void
    {
        // === Organisation ===
        $org = $this->organisationService->get($user);
        if ($org) {
            XeroOrganisation::updateOrCreate(
                ['user_id' => $user->id, 'xero_id' => $org->getOrganisationID()],
                [
                    'name' => $org->getName() ?? '',
                    'country_code' => $org->getCountryCode() ?? '',
                ]
            );
        }

        // === Invoices (limit 5) ===
        $invoices = $this->invoiceService->get($user, $token, 5);
        foreach ($invoices as $inv) {
            XeroInvoice::updateOrCreate(
                ['user_id' => $user->id, 'xero_id' => $inv->getInvoiceID()],
                [
                    'number' => $inv->getInvoiceNumber() ?? '',
                    'date' => optional(XeroHelper::parseXeroDate($inv->getDate()))->format('Y-m-d'),
                    'amount' => $inv->getTotal() ?? 0,
                    'status' => $inv->getStatus() ?? '',
                ]
            );
        }

        // === Contacts (limit 5) ===
        $contacts = $this->contactService->get($user, $token, 5);
        foreach ($contacts as $contact) {
            XeroContact::updateOrCreate(
                ['user_id' => $user->id, 'xero_id' => $contact->getContactID()],
                [
                    'name' => $contact->getName() ?? '',
                    'email' => $contact->getEmailAddress() ?? null,
                ]
            );
        }

        // === Bank Accounts (no balance API in v10) ===
        $accounts = $this->bankAccountService->get($user, $token);
        foreach ($accounts as $acc) {
            XeroBankAccount::updateOrCreate(
                ['user_id' => $user->id, 'xero_id' => $acc->getAccountID()],
                [
                    'name' => $acc->getName() ?? '',
                    'balance' => $acc->balance, // getBankAccountBalances() not available in SDK v10, I use a workaround in the service
                ]
            );
        }
    }
}
