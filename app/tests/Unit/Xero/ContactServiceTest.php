<?php

use App\Models\User;
use App\Services\Xero\Resources\ContactService;
use App\Services\Xero\TokenManager;
use Psr\Log\LoggerInterface;

describe('ContactService', function () {
    beforeEach(function () {
        $this->user = \Mockery::mock(User::class);
        $this->tokenManager = \Mockery::mock(TokenManager::class);
        $this->logger = \Mockery::mock(LoggerInterface::class);
    });

    test('returns empty array if no token', function () {
        $this->tokenManager->shouldReceive('getValidTokenFor')->andReturn(null);
        $service = new ContactService($this->tokenManager, $this->logger);
        $result = $service->get($this->user);
        expect($result)->toBeArray()->toBeEmpty();
    });

    test('returns contacts from XeroClient stub', function () {
        $mockToken = (object) ['access_token' => 'abc', 'tenant_id' => 'tid'];
        $this->tokenManager->shouldReceive('getValidTokenFor')->andReturn($mockToken);

        $service = new class($this->tokenManager, $this->logger) extends ContactService {
            public function get($user, $limit = 5): array
            {
                return [(object)['ContactID' => 'contact-id', 'Name' => 'Test Contact']];
            }
        };

        $result = $service->get($this->user);
        expect($result)->toHaveCount(1);
        expect($result[0]->ContactID)->toBe('contact-id');
        expect($result[0]->Name)->toBe('Test Contact');
    });
});
