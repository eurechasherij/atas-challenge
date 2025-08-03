import React from 'react';

type Invoice = { number: string; date: string; amount: number; status: string };

interface InvoicesTableProps {
    invoices: Invoice[];
}

const InvoicesTable: React.FC<InvoicesTableProps> = ({ invoices }) => (
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
);

export default InvoicesTable;
