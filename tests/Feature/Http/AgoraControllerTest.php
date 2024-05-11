<?php

namespace AbdullahFaqeir\LaravelAgoraApi\Tests\Feature\Http;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Mockery;
use AbdullahFaqeir\Authorization\Models\User;
use AbdullahFaqeir\LaravelAgoraApi\Events\AgoraCallAccepted;
use AbdullahFaqeir\LaravelAgoraApi\Events\DispatchAgoraCall;
use AbdullahFaqeir\LaravelAgoraApi\Events\RejectAgoraCall;
use AbdullahFaqeir\LaravelAgoraApi\Tests\TestCase;

class AgoraControllerTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    public function testUnauthenticatedUsersCannotRetrieveAToken()
    {
        $response = $this->postJson(route('laravel-agora-api.retrieve-token'));

        $response->assertStatus(401);
    }

    public function testUnauthenticatedUsersCannotPlaceACall()
    {
        $response = $this->postJson(route('laravel-agora-api.place-call'));

        $response->assertStatus(401);
    }

    public function testInvalidRequestsDoNotReturnAToken()
    {
        $response = $this->actingAs(User::factory()->create())
            ->postJson(route('laravel-agora-api.retrieve-token'));

        $response->assertStatus(422);
    }

    public function testInvalidRequestsDoNotReturnPlaceACall()
    {
        $response = $this->actingAs(User::factory()->create())
            ->postJson(route('laravel-agora-api.place-call'));

        $response->assertStatus(422);
    }

    public function testAuthorizedUsersCanRetrieveAToken()
    {
        $fakeTokenContents = 'an-Agora-token';

        Mockery::namedMock('AbdullahFaqeir\LaravelAgoraApi\AgoraDynamicKey\RtcTokenBuilder', 'AbdullahFaqeir\LaravelAgoraApi\Tests\Feature\Http\RtcTokenBuilderStub')
            ->shouldReceive('buildTokenWithUid')
            ->andReturn($fakeTokenContents);

        $user = User::factory()->create();
        $user->name = 'John Doe';

        $response = $this->actingAs($user)
            ->postJson(route('laravel-agora-api.retrieve-token'), [
                'channel_name' => $this->faker->word,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'token' => $fakeTokenContents,
            ]);

        Mockery::close();
    }

    public function testAuthorizedUsersCanPlaceACall()
    {
        Event::fake();

        $user = User::factory()->create();
        $user->name = 'John Doe';

        $response = $this->actingAs($user)
            ->postJson(route('laravel-agora-api.place-call'), [
                'channel_name' => $this->faker->word,
                'recipient_id' => User::factory()->create()->id,
            ]);

        Event::assertDispatched(DispatchAgoraCall::class);

        $response->assertStatus(200);
    }

    public function testCallAcceptanceEventIsDispatchable()
    {
        Event::fake();

        $user = User::factory()->create();
        $user->name = 'John Doe';

        $response = $this->actingAs($user)
            ->postJson(route('laravel-agora-api.accept-call'), [
                'caller_id' => User::factory()->create()->id,
                'recipient_id' => $user->id,
            ]);

        Event::assertDispatched(AgoraCallAccepted::class);

        $response->assertStatus(200);
    }

    public function testCallAcceptanceEventIsNotDispatchedWhenMissingData()
    {
        Event::fake();

        $user = User::factory()->create();
        $user->name = 'John Doe';

        $response = $this->actingAs($user)
            ->postJson(route('laravel-agora-api.accept-call'), []);

        Event::assertNotDispatched(AgoraCallAccepted::class);

        $response->assertStatus(422);
    }

    public function testCallRejectionEventIsDispatchable()
    {
        Event::fake();

        $user = User::factory()->create();
        $user->name = 'John Doe';

        $response = $this->actingAs($user)
            ->postJson(route('laravel-agora-api.reject-call'), [
                'caller_id' => User::factory()->create()->id,
                'recipient_id' => $user->id,
            ]);

        Event::assertDispatched(RejectAgoraCall::class);

        $response->assertStatus(200);
    }

    public function testCallRejectionEventIsNotDispatchedWhenMissingData()
    {
        Event::fake();

        $user = User::factory()->create();
        $user->name = 'John Doe';

        $response = $this->actingAs($user)
            ->postJson(route('laravel-agora-api.reject-call'), []);

        Event::assertNotDispatched(RejectAgoraCall::class);

        $response->assertStatus(422);
    }
}

class RtcTokenBuilderStub
{
    const RoleAttendee = 0;
    const RolePublisher = 1;
    const RoleSubscriber = 2;
    const RoleAdmin = 101;
}
