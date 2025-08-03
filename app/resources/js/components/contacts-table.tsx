import React from 'react';

type Contact = { name: string; email: string };

interface ContactsTableProps {
    contacts: Contact[];
}

const ContactsTable: React.FC<ContactsTableProps> = ({ contacts }) => (
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
                            <td className="border px-2 py-1">{c.email || "-"}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
        )}
    </div>
);

export default ContactsTable;
