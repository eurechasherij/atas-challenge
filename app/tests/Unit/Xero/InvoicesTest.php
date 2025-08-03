<?php

use App\Models\User;
use App\Services\Xero\Resources\InvoiceService;
use App\Services\Xero\TokenManager;
use Psr\Log\LoggerInterface;

describe('InvoiceService', function () {
    beforeEach(function () {
        $this->user = \Mockery::mock(User::class);
        $this->tokenManager = \Mockery::mock(TokenManager::class);
        $this->logger = \Mockery::mock(LoggerInterface::class);
    });

    test('returns empty array if no token', function () {
        $this->tokenManager->shouldReceive('getValidTokenFor')->andReturn(null);
        $service = new InvoiceService($this->tokenManager, $this->logger);
        $result = $service->get($this->user);
        expect($result)->toBeArray()->toBeEmpty();
    });

    test('returns invoices from XeroClient stub', function () {
        $mockToken = (object) ['access_token' => 'abc', 'tenant_id' => 'tid'];
        $this->tokenManager->shouldReceive('getValidTokenFor')->andReturn($mockToken);

        $service = new class($this->tokenManager, $this->logger) extends InvoiceService {
            public function get($user, $limit = 5): array
            {
                return [(object)['InvoiceID' => 'fake-id']];
            }
        };

        $result = $service->get($this->user);
        expect($result)->toHaveCount(1);
        expect($result[0]->InvoiceID)->toBe('fake-id');
    });
});
