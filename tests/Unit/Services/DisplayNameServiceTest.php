<?php

namespace AbdullahFaqeir\LaravelAgoraApi\Tests\Unit\Services;

use Exception;
use Illuminate\Support\Facades\Config;
use stdClass;
use AbdullahFaqeir\LaravelAgoraApi\Services\DisplayNameService;
use AbdullahFaqeir\LaravelAgoraApi\Tests\TestCase;

class AgoraControllerTest extends TestCase
{
    public function testExceptionThrownOnNonexistantFieldName()
    {
        $user = new stdClass();

        $this->expectException(Exception::class);

        $this->expectExceptionMessage("Nonexistent object property: 'name' specified in username generation configuration.");

        DisplayNameService::getDisplayName($user);
    }

    public function testGenerateDisplayNameFromSingleField()
    {
        $user = new stdClass();
        $user->name = 'John';

        $displayName = DisplayNameService::getDisplayName($user);

        $this->assertEquals($user->name, $displayName);
    }

    public function testGenerateDisplayNameFromMultipleFields()
    {
        Config::set('laravel-agora-api.user_display_name.fields', [
            'first_name',
            'last_name',
        ]);

        $user = new stdClass();
        $user->first_name = 'John';
        $user->last_name = 'Stephenson';

        $displayName = DisplayNameService::getDisplayName($user);

        $this->assertEquals($user->first_name.' '.$user->last_name, $displayName);
    }
}
