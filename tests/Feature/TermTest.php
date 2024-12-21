<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\user;
use App\Models\Term;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TermTest extends TestCase
{
    use RefreshDatabase;
// 利用規約ページ
   // 未ログインのユーザーは会員側の利用規約ページにアクセスできる
   public function test_guest_can_access_user_terms_of_use_index()
   {
       $term = Term::factory()->create();

       $response = $this->get(route('terms.index', $term));

       $response->assertStatus(200);
   }
   // ログイン済みの一般ユーザーは会員側の利用規約ページにアクセスできる
   public function test_user_can_access_user_terms_of_use_index()
   {
       $term = Term::factory()->create();
       $user = User::factory()->create();

       $response = $this->actingAs($user)->get(route('terms.index', $term));

       $response->assertStatus(200);
   }
   // ログイン済みの管理者は会員側の利用規約ページにアクセスできない
   public function test_admin_cannot_access_user_company_profile_index()
   { 
      $term = Term::factory()->create();
      
      $admin = new Admin();
      $admin->email = 'admin@example.com';
      $admin->password = Hash::make('nagoyameshi');
      $admin->save();

      $response = $this->actingAs($admin, 'admin')->get(route('terms.index', $term));

      $response->assertRedirect(route('admin.home'));
   }
}



    