<?php

namespace Tests\Feature\Http\Controllers\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthControllerTest extends TestCase {

    use RefreshDatabase;

    protected function setUp(): void{

        parent::setUp();
        $this->artisan('passport:install');

    }

    /**
     * @test
     */
    public function can_authenticate(){
        $response = $this->json('POST','/auth/token',[
            'email' => $this->create('User',[],false)->email,
            'password' => 'password'
        ]);

        $response
        ->assertJsonStructure(['token'])
        ->assertStatus(200);

        \Log::info($response->getContent());
    }
}
