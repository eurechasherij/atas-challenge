import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { CheckIcon } from 'lucide-react';
import ManualSyncButton from '@/components/manual-sync-button';
import InvoicesTable from '@/components/invoices-table';
import ContactsTable from '@/components/contacts-table';
import BankAccountsTable from '@/components/bank-accounts-table';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

type Invoice = { number: string; date: string; amount: number; status: string };
type Contact = { name: string; email: string };
type BankAccount = { name: string; balance: number };
type Org = { name: string; country: string } | null;

interface DashboardProps {
    org: Org;
    invoices?: Invoice[];
    contacts?: Contact[];
    bankAccounts?: BankAccount[];
}

const Dashboard = ({ org, invoices = [], contacts = [], bankAccounts = [] }: DashboardProps) => {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="space-y-4 p-6">
                <h1 className="text-xl font-bold">Atas Challenge</h1>

                {org ? (
                    <div className="space-y-4">
                        <div>
                            <p className="flex flex-row gap-2 font-semibold text-green-600"><CheckIcon /> Connected to Xero</p>
                            <p className="text-sm text-gray-500">Organisation Name: {org.name}</p>
                            <p className="text-sm text-gray-500">Country: {org.country}</p>
                        </div>

                        <ManualSyncButton />

                        <InvoicesTable invoices={invoices} />
                        <ContactsTable contacts={contacts} />
                        <BankAccountsTable bankAccounts={bankAccounts} />
                    </div>
                ) : (
                    <a href="/xero/login" className="inline-block rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                        Connect to Xero
                    </a>
                )}
            </div>
        </AppLayout>
    );
};

export default Dashboard;
