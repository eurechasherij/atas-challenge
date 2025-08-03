import { Head, router } from '@inertiajs/react';
import React from 'react';

interface Tenant {
    tenantId: string;
    tenantName: string;
    [key: string]: any;
}

interface Props {
    tenants: Tenant[];
}

const SelectTenant: React.FC<Props> = ({ tenants }) => {
    const [selected, setSelected] = React.useState(tenants[0]?.tenantId || '');
    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        router.post('/xero/select-tenant', { tenantId: selected });
    };
    return (
        <div className="p-8 max-w-lg mx-auto">
            <Head title="Select Xero Tenant" />
            <h1 className="text-xl font-bold mb-4">Select a Xero Tenant</h1>
            <form onSubmit={handleSubmit} className="space-y-4">
                <select
                    className="w-full border rounded px-3 py-2"
                    value={selected}
                    onChange={e => setSelected(e.target.value)}
                >
                    {tenants.map(t => (
                        <option key={t.tenantId} value={t.tenantId}>{t.tenantName}</option>
                    ))}
                </select>
                <button type="submit" className="w-full bg-blue-600 text-white rounded px-4 py-2 hover:bg-blue-700">Continue</button>
            </form>
        </div>
    );
};

export default SelectTenant;
