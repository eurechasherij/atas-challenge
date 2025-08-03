import React from 'react';

type BankAccount = { name: string; balance: number };

interface BankAccountsTableProps {
    bankAccounts: BankAccount[];
}

const BankAccountsTable: React.FC<BankAccountsTableProps> = ({ bankAccounts }) => (
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
                            <td className="border px-2 py-1">${b.balance || 0}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
        )}
    </div>
);

export default BankAccountsTable;
