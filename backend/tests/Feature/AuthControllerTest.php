<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Redis;
use Laravel\Socialite\Facades\Socialite;
use Tests\TestCase;
use App\Models\User;
use Mockery;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    //Mock chain helper, repeated usecase
    private function mockSocialiteUser($githubUserMock): void
    {
        $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('stateless')->andReturn($provider);
        $provider->shouldReceive('user')->andReturn($githubUserMock);

        Socialite::shouldReceive('driver')
            ->with('github')
            ->andReturn($provider);
    }

    public function test_redirect_to_github_redirects_successfully()
    {
        Redis::shouldReceive('incr')->once()->andReturn(1);
        Redis::shouldReceive('expire')->once();

        $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('stateless')->andReturn($provider);
        $provider->shouldReceive('redirect')->andReturn(redirect('https://github.com'));

        Socialite::shouldReceive('driver')->with('github')->andReturn($provider);

        $response = $this->get('api/auth/github/redirect');
        $response->assertRedirect('https://github.com');
    }

    public function test_redirect_blocks_after_too_many_attempts()
    {
        Redis::shouldReceive('incr')->once()->andReturn(6);
        $response = $this->get('api/auth/github/redirect');
        $response->assertStatus(429)
            ->assertJson(['message' => 'Too many login attempts. Chill out.']);
    }

    public function test_logout()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test_token')->plainTextToken;
        $response = $this->withToken($token)->post('api/logout');
        $response->assertStatus(200)->assertJson(['message' => 'Logged out successfully']);
        $this->assertCount(0, $user->tokens);
    }

    public function test_callback_creates_new_user_if_not_exists()
    {
        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser->id = '12345678'; // Fake GitHub ID
        $abstractUser->email = 'newuser@example.com';
        $abstractUser->name = 'New User';
        $abstractUser->nickname = 'newuser';
        $abstractUser->avatar = 'https://avatar.url';
        $this->mockSocialiteUser($abstractUser);

        $response = $this->get('api/auth/github/callback');

        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
            'github_id' => '12345678',
        ]);
        $response->assertStatus(200);
    }

    public function test_logs_in_existing_user()
    {
        $user = User::factory()->create([
            "github_id" => "88888888",
            'email' => 'old@example.com'
        ]);

        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser->id = "88888888";
        $abstractUser->email = 'old@example.com';
        $abstractUser->name = "Old User";
        $abstractUser->nickname = 'olduser';
        $abstractUser->avatar = "https://avatar.url";
        $this->mockSocialiteUser($abstractUser);
        $response = $this->get('api/auth/github/callback');
        $response->assertStatus(200);
        $this->assertDatabaseCount('users', 1);
        $response->assertJsonStructure(['token', 'user', 'message']);
    }

    public function test_links_user_by_email_if_id_is_missing()
    {
        $user = User::factory()->create([
            "github_id" => '12345678',
            "email" => "match@gmail.com"
        ]);

        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser->id = "99999999";
        $abstractUser->email = "match@gmail.com";
        $abstractUser->name = "Linked User";
        $abstractUser->nickname = 'linked';
        $abstractUser->avatar = "https://new-avatar.url";

        $this->mockSocialiteUser($abstractUser);
        $this->get('api/auth/github/callback');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'match@gmail.com',
            'github_id' => '99999999',
            'avatar' => 'https://new-avatar.url'
        ]);
    }
}
