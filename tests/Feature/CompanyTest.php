<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\user;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

// 会社概要ページ
   // 未ログインのユーザーは会員側の会社概要ページにアクセスできる
   public function test_guest_can_access_user_company_profile_index()
   {
       $company = Company::factory()->create();

       $response = $this->get(route('company.index', $company));

       $response->assertStatus(200);
   }
   // ログイン済みの一般ユーザーは会員側の会社概要ページにアクセスできる
   public function test_user_can_access_user_company_profile_index()
   {
       $company = Company::factory()->create();
       $user = User::factory()->create();

       $response = $this->actingAs($user)->get(route('company.index', $company));

       $response->assertStatus(200);
    }
   // ログイン済みの管理者は会員側の会社概要ページにアクセスできない
   public function test_admin_cannot_access_user_company_profile_index()
   {
      $company = Company::factory()->create();

      $admin = new Admin();
      $admin->email = 'admin@example.com';
      $admin->password = Hash::make('nagoyameshi');
      $admin->save();

      $response = $this->actingAs($admin, 'admin')->get(route('company.index', $company));

      $response->assertRedirect(route('admin.home'));
   }
}
