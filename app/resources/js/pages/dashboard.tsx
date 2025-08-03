import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { CheckIcon } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const Dashboard = ({ org }) => {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="space-y-4 p-6">
                <h1 className="text-xl font-bold">Welcome to Atas Challenge</h1>

                {org ? (
                    <div>
                        <p className="flex flex-row gap-2 font-semibold text-green-600"><CheckIcon /> Connected to Xero</p>
                        <p className="text-sm text-gray-500">Organisation Name: {org.name}</p>
                        <p className="text-sm text-gray-500">Country: {org.country}</p>
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
