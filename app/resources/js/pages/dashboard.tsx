import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { CheckIcon } from 'lucide-react';
import ManualSyncButton from '@/components/manual-sync-button';

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

                        <div className="mt-6">
                            <h2 className="text-lg font-semibold mb-2">Recent Invoices</h2>
                            {invoices.length === 0 ? <p className="text-gray-400">No invoices found.</p> : (
                                <table className="min-w-full border text-sm">
                                    <thead>
                                        <tr className="bg-gray-100 dark:bg-gray-600 [&>th]:text-start">
                                            <th className="px-2 py-1 border">Number</th>
                                            <th className="px-2 py-1 border">Date</th>
                                            <th className="px-2 py-1 border">Amount</th>
                                            <th className="px-2 py-1 border">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {invoices.map((inv, i) => (
                                            <tr key={i}>
                                                <td className="border px-2 py-1">{inv.number}</td>
                                                <td className="border px-2 py-1">{inv.date}</td>
                                                <td className="border px-2 py-1">${inv.amount}</td>
                                                <td className="border px-2 py-1">{inv.status}</td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            )}
                        </div>

                        <div className="mt-6">
                            <h2 className="text-lg font-semibold mb-2">Recent Contacts</h2>
                            {contacts.length === 0 ? <p className="text-gray-400">No contacts found.</p> : (
                                <table className="min-w-full border text-sm">
                                    <thead>
                                        <tr className="bg-gray-100 dark:bg-gray-600 [&>th]:text-start">
                                            <th className="px-2 py-1 border">Name</th>
                                            <th className="px-2 py-1 border">Email</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {contacts.map((c, i) => (
                                            <tr key={i}>
                                                <td className="border px-2 py-1">{c.name}</td>
                                                <td className="border px-2 py-1">{c.email}</td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            )}
                        </div>

                        <div className="mt-6">
                            <h2 className="text-lg font-semibold mb-2">Bank Accounts</h2>
                            {bankAccounts.length === 0 ? <p className="text-gray-400">No bank accounts found.</p> : (
                                <table className="min-w-full border text-sm">
                                    <thead>
                                        <tr className="bg-gray-100 dark:bg-gray-600 [&>th]:text-start">
                                            <th className="px-2 py-1 border">Name</th>
                                            <th className="px-2 py-1 border">Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {bankAccounts.map((b, i) => (
                                            <tr key={i}>
                                                <td className="border px-2 py-1">{b.name}</td>
                                                <td className="border px-2 py-1">${b.balance}</td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            )}
                        </div>
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
