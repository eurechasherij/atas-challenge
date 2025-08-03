<?php

return [
    'client_id' => env('XERO_CLIENT_ID'),
    'client_secret' => env('XERO_CLIENT_SECRET'),
    'redirect_uri' => env('XERO_REDIRECT_URI', 'http://localhost:8000/xero/callback'),
    'scopes' => env('XERO_SCOPES', 'openid profile email accounting.transactions accounting.settings accounting.reports.read accounting.contacts'),
];