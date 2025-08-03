
import React from 'react';
import { router } from '@inertiajs/react';

const ManualSyncButton: React.FC = () => {
    const [loading, setLoading] = React.useState(false);
    const handleSync = (e: React.MouseEvent<HTMLButtonElement>) => {
        e.preventDefault();
        setLoading(true);
        router.post('/xero/sync', {}, {
            onFinish: () => setLoading(false),
            preserveScroll: true,
        });
    };
    return (
        <button
            type="button"
            className="mt-2 rounded bg-blue-500 px-4 py-2 text-white hover:bg-blue-600 disabled:opacity-60"
            onClick={handleSync}
            disabled={loading}
        >
            {loading ? 'Syncing...' : 'Manual Sync'}
        </button>
    );
};

export default ManualSyncButton;
