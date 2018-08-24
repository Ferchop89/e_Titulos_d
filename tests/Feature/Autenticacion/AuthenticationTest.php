<?php

namespace Tests\Feature\Autenticacion;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_shows_the_dashboard_page_to_authenticated_users(){
        $user = factory(User::class)->create();

        $this->actingAs($user)
            ->get(route('home'))
            ->assertSee('Dashboard')
            ->assertStatus(200);
    }

    /** @test */
    function it_redirects_guest_users_to_the_login_page(){
        $this->get(route('home'))
            ->assertStatus(302)
            ->assertRedirect('login');
    }
}
