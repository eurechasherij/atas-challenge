import { router } from '@inertiajs/react';
import React from 'react';

const ManualSyncButton: React.FC = () => {
    const [loading, setLoading] = React.useState(false);
    const handleSync = (e: React.MouseEvent<HTMLButtonElement>) => {
        e.preventDefault();
        setLoading(true);
        router.post(
            '/xero/sync',
            {},
            {
                onFinish: () => setLoading(false),
                preserveScroll: true,
            },
        );
    };
    return (
        <button
            type="button"
            className="mt-2 rounded border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b] disabled:opacity-60"
            onClick={handleSync}
            disabled={loading}
        >
            {loading ? 'Syncing...' : 'Manual Sync'}
        </button>
    );
};

export default ManualSyncButton;
