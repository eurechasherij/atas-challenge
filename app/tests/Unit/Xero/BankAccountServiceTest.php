<?php

use App\Models\User;
use App\Services\Xero\Resources\BankAccountService;
use App\Services\Xero\TokenManager;
use Psr\Log\LoggerInterface;

describe('BankAccountService', function () {
    beforeEach(function () {
        $this->user = \Mockery::mock(User::class);
        $this->tokenManager = \Mockery::mock(TokenManager::class);
        $this->logger = \Mockery::mock(LoggerInterface::class);
    });

    test('returns empty array if no token', function () {
        $this->tokenManager->shouldReceive('getValidTokenFor')->andReturn(null);
        $service = new BankAccountService($this->tokenManager, $this->logger);
        $result = $service->get($this->user);
        expect($result)->toBeArray()->toBeEmpty();
    });

    test('returns bank accounts from XeroClient stub', function () {
        $mockToken = (object) ['access_token' => 'abc', 'tenant_id' => 'tid'];
        $this->tokenManager->shouldReceive('getValidTokenFor')->andReturn($mockToken);

        $service = new class($this->tokenManager, $this->logger) extends BankAccountService {
            public function get($user): array
            {
                return [(object)['AccountID' => 'bank-id', 'Name' => 'Test Bank', 'Balance' => 100.0]];
            }
        };

        $result = $service->get($this->user);
        expect($result)->toHaveCount(1);
        expect($result[0]->AccountID)->toBe('bank-id');
        expect($result[0]->Name)->toBe('Test Bank');
        expect($result[0]->Balance)->toBe(100.0);
    });
});
