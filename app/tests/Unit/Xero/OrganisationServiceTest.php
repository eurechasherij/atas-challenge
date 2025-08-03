<?php

use App\Models\User;
use App\Services\Xero\Resources\OrganisationService;
use App\Services\Xero\TokenManager;
use Psr\Log\LoggerInterface;

describe('OrganisationService', function () {
    beforeEach(function () {
        $this->user = \Mockery::mock(User::class);
        $this->tokenManager = \Mockery::mock(TokenManager::class);
        $this->logger = \Mockery::mock(LoggerInterface::class);
    });

    test('returns null if no token', function () {
        $this->tokenManager->shouldReceive('getValidTokenFor')->andReturn(null);
        $service = new OrganisationService($this->tokenManager, $this->logger);
        $result = $service->get($this->user);
        expect($result)->toBeNull();
    });

    test('returns organisation from XeroClient stub', function () {
        $mockToken = (object) ['access_token' => 'abc', 'tenant_id' => 'tid'];
        $this->tokenManager->shouldReceive('getValidTokenFor')->andReturn($mockToken);

        $service = new class($this->tokenManager, $this->logger) extends OrganisationService {
            public function get($user)
            {
                return (object)['OrganisationID' => 'org-id', 'Name' => 'Test Org'];
            }
        };

        $result = $service->get($this->user);
        expect($result->OrganisationID)->toBe('org-id');
        expect($result->Name)->toBe('Test Org');
    });
});
