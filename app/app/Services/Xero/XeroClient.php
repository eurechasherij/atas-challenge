<?php

namespace App\Services\Xero;

use XeroAPI\XeroPHP\Configuration;
use XeroAPI\XeroPHP\Api\AccountingApi;
use GuzzleHttp\Client;

class XeroClient
{
    public readonly AccountingApi $accounting;

    public function __construct(string $accessToken)
    {
        $config = Configuration::getDefaultConfiguration()
            ->setAccessToken($accessToken)
            ->setHost('https://api.xero.com/api.xro/2.0');

        $guzzle = new Client([
            'headers' => [
                'Authorization' => "Bearer {$accessToken}",
                'Accept' => 'application/json',
            ],
        ]);

        $this->accounting = new AccountingApi($guzzle, $config);
    }
}
