<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

// 会社概要ページ
    // 未ログインのユーザーは管理者側の会社概要ページにアクセスできない
    public function test_guest_cannot_access_admin_company_index()
     {
         $response = $this->get(route('admin.company.index'));
 
         $response->assertRedirect(route('admin.login'));
     }
    // ログイン済みの一般ユーザーは管理者側の会社概要ページにアクセスできない
    public function test_user_cannot_access_admin_company_index()
     {
         $user = User::factory()->create();
 
         $response = $this->actingAs($user)->get(route('admin.company.index'));
 
         $response->assertRedirect(route('admin.login'));
     }
    // ログイン済みの管理者は管理者側の会社概要ページにアクセスできる
    public function test_admin_can_access_admin_company_index()
     {
         $admin = new Admin();
         $admin->email = 'admin@example.com';
         $admin->password = Hash::make('nagoyameshi');
         $admin->save();

         $company = Company::factory()->create();
 
         $response = $this->actingAs($admin, 'admin')->get(route('admin.company.index'));
 
         $response->assertStatus(200);
     }


// 会社概要編集ページ
    // 未ログインのユーザーは管理者側の会社概要編集ページにアクセスできない
    public function test_guest_cannot_access_admin_company_edit()
     {
         $company = Company::factory()->create();

         $response = $this->get(route('admin.company.edit',$company));
 
         $response->assertRedirect(route('admin.login'));
     }
    // ログイン済みの一般ユーザーは管理者側の会社概要編集ページにアクセスできない
    public function test_user_cannot_access_admin_company_edit()
     {
         $user = User::factory()->create();
         $company = Company::factory()->create();
 
         $response = $this->actingAs($user)->get(route('admin.company.edit',$company));
 
         $response->assertRedirect(route('admin.login'));
     }
    // ログイン済みの管理者は管理者側の会社概要編集ページにアクセスできる
    public function test_admin_can_access_admin_company_edit()
     {
         $admin = new Admin();
         $admin->email = 'admin@example.com';
         $admin->password = Hash::make('nagoyameshi');
         $admin->save();

         $company = Company::factory()->create();
 
         $response = $this->actingAs($admin, 'admin')->get(route('admin.company.edit',$company));
 
         $response->assertStatus(200);
     }


// 会社概要更新機能
     //  未ログインのユーザーは会社概要を更新できない
     public function test_guest_cannot_update_company()
     {
        $old_company = Company::factory()->create();

         $update_company = [
            'name' => 'テスト2',
            'postal_code' => '1234567',
            'address' => 'テスト2', 
            'representative' => 'テスト2',
            'establishment_date' => 'テスト2',
            'capital' => 'テスト2',
            'business' => 'テスト2',
            'number_of_employees' => 'テスト2'
           ];

         $response = $this->patch(route('admin.company.update', $old_company),$update_company);
         
         $response->assertRedirect(route('admin.login'));
      }
      // ログイン済みの一般ユーザーは会社概要を更新できない
     public function test_user_cannot_update_company()
      {
         $user = User::factory()->create();
         $old_company = Company::factory()->create();

         $update_company = [
            'name' => 'テスト2',
            'postal_code' => '1234567',
            'address' => 'テスト2', 
            'representative' => 'テスト2',
            'establishment_date' => 'テスト2',
            'capital' => 'テスト2',
            'business' => 'テスト2',
            'number_of_employees' => 'テスト2'
         ];

         $response = $this->actingAs($user)->patch(route('admin.company.update', $old_company),$update_company);
         
         $response->assertRedirect(route('admin.login'));
     }
 
     // ログイン済みの管理者は会社概要を更新できる
        public function test_admin_user_can_update_company()
        {
            $admin = new Admin();
            $admin->email = 'admin@example.com';
            $admin->password = Hash::make('nagoyameshi');
            $admin->save();

            $old_company = Company::factory()->create();
    
            $update_company = [
                'name' => 'テスト2',
                'postal_code' => '1234567',
                'address' => 'テスト2', 
                'representative' => 'テスト2',
                'establishment_date' => 'テスト2',
                'capital' => 'テスト2',
                'business' => 'テスト2',
                'number_of_employees' => 'テスト2'
             ];

        $response = $this->actingAs($admin, 'admin')->patch(route('admin.company.update', $old_company), $update_company);
       
        $response->assertRedirect(route('admin.company.index'));
    }
}
          
