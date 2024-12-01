<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class UserTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_not_adminuser_cannot_access_adminuser_list()
    {
        $user = User::factory()->create();
       
        $response =$this->actingAs($user)->get('/admin/users');
        $response ->assertRedirect('admin/login');
    }

    public function test_adminuser_can_access_adminuser_list()
    {
        $adminUser =User::factory()->create(['email'=>'admin@example.com']);
    
        $response=$this->actingAs($adminUser, 'admin')->get('/admin/users');
        $response->assertStatus(200);
    }

    public function test_not_adminuser_cannot_access_adminuser_detail()
    {
        $user =User::factory()->create();

        $response=$this->actingAs($user)->get(route('admin.users.show', $user));
        $response->assertRedirect('admin/login');
    }

    public function test_adminuser_can_access_adminuser_detail()
    {
        $adminUser =User::factory()->create(['email'=>'admin@example.com']);
        $user =User::factory()->create();

        $response=$this->actingAs($adminUser,'admin')->get(route('admin.users.show', $user));
       
        $response->assertStatus(200);
    }
}
