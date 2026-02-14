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

    public function test_redirect_to_github_redirects_successfully()
    {
        Redis::shouldReceive('incr')->once()->andReturn(1);
        Redis::shouldReceive('expire')->once();
        Socialite::shouldReceive('driver')
            ->with('github')
            ->andReturn($driver = Mockery::mock('Laravel\Socialite\Contracts\Provider'));

        $driver->shouldReceive('stateless->redirect')
            ->andReturn(redirect('https://github.com/login/oauth/authorize'));
        $response = $this->get('/auth/github');
        $response->assertRedirect();
    }

    public function test_redirect_blocks_after_too_many_attempts()
    {
        Redis::shouldReceive('incr')->once()->andReturn(6);
        $response = $this->get('/auth/github');
        $response->assertStatus(429)
            ->assertJson(['message' => 'Too many login attempts. Chill out.']);
    }

    public function test_logout(){
        $user = User::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->post('/logout');
        $response->assertStatus(200)->assertJson(['message' => 'Logged out successfully.']);
        $this->assertCount(0, $user->tokens);
    }

    public function test_redirect_blocks_after_too_many__attempts(){
        Redis::shouldReceive('incr')->once()->andReturn(6);
        $response = $this->get('/auth/github');
        $response->assertStatus(429)->assertJson(['message' => 'Too many login attempts. Chill out.']);
    }

    public function test_callback_creates_new_user_if_not_exists(){
        $githubId = '12345678';
        $email = 'newuser@example.com';

        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser->id = $githubId;
        $abstractUser->email = $email;
        $abstractUser->name = 'New User';
        $abstractUser->avatar = 'https://avatar.url';


    }
}
