<?php

namespace Tests\Feature\Http\Controllers\Auth;

use Mockery;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthControllerTest extends TestCase {

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('passport:install');
    }

    /**
     * @test
     */
    public function can_authenticate()
    {
        $response = $this->json('POST','/auth/token',[
            'email' => $this->create('User',[],false)->email,
            'password' => 'password'
        ]);

        $response
        ->assertJsonStructure(['token'])
        ->assertStatus(200);

        \Log::info($response->getContent());
    }

    /**
     * @test
     */
    public function can_authenticate_using_google()
    {
        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser->shouldReceive('getId')
        ->andReturn(rand())
        ->shouldReceive('getEmail')
        ->andReturn('juan@gmail.com')
        ->shouldReceive('getName')
        ->andReturn('Javier Corvera')
        ->shouldReceive('getAvatar')
        ->andReturn('https://en.gravatar.com/userimage');

        $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->andReturn($provider);

        $this->get('/social/auth/google/callback')
        ->assertStatus(302);
    }

}
