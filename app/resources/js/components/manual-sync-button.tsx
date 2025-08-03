import { router } from '@inertiajs/react';
import React from 'react';

const ManualSyncButton: React.FC = () => {
    const [loading, setLoading] = React.useState(false);
    const [success, setSuccess] = React.useState(false);
    const [error, setError] = React.useState(false);
    const handleSync = (e: React.MouseEvent<HTMLButtonElement>) => {
        e.preventDefault();
        setLoading(true);
        setSuccess(false);
        setError(false);
        router.post(
            '/xero/sync',
            {},
            {
                onFinish: () => setLoading(false),
                onSuccess: () => {
                    setSuccess(true);
                    setTimeout(() => setSuccess(false), 3000);
                },
                onError: () => {
                    setError(true);
                    setTimeout(() => setError(false), 3000);
                },
                preserveScroll: true,
            },
        );
    };
    return (
        <>
            <button
                type="button"
                className="mt-2 rounded border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] disabled:opacity-60 dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                onClick={handleSync}
                disabled={loading}
            >
                {loading ? 'Syncing...' : 'Manual Sync'}
            </button>

            {!loading && success && <span className="ml-2 text-green-500">Sync successful!</span>}
            {!loading && error && <span className="ml-2 text-red-500">Sync failed. Please try again.</span>}
        </>
    );
};

export default ManualSyncButton;
