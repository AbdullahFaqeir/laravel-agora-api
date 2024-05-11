<?php

declare(strict_types=1);

namespace AbdullahFaqeir\LaravelAgoraApi\Tests;

use Spatie\Permission\PermissionServiceProvider;
use AbdullahFaqeir\Authorization\AuthorizationServiceProvider;
use AbdullahFaqeir\LaravelAgoraApi\LaravelAgoraApiServiceProvider;
use AbdullahFaqeir\Support\SupportServiceProvider;
use AbdullahFaqeir\TestSupport\BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            SupportServiceProvider::class,
            AuthorizationServiceProvider::class,
            PermissionServiceProvider::class,
            LaravelAgoraApiServiceProvider::class,
        ];
    }
}
