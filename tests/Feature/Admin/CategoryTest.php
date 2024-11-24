<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Category;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


class CategoryTest extends TestCase
{
    use RefreshDatabase;

// カテゴリ一覧ページ
     // 未ログインのユーザーは管理者側のカテゴリ一覧ページにアクセスできない
     public function test_guest_cannot_access_admin_categories_index()
     {
         $response = $this->get(route('admin.categories.index'));
 
         $response->assertRedirect(route('admin.login'));
     }
 
     // ログイン済みの一般ユーザーは管理者側のカテゴリ一覧ページにアクセスできない
     public function test_user_cannot_access_admin_categories_index()
     {
         $user = User::factory()->create();
 
         $response = $this->actingAs($user)->get(route('admin.categories.index'));
 
         $response->assertRedirect(route('admin.login'));
     }
 
     // ログイン済みの管理者は管理者側のカテゴリ一覧ページにアクセスできる
     public function test_admin_can_access_admin_categories_index()
     {
         $admin = new Admin();
         $admin->email = 'admin@example.com';
         $admin->password = Hash::make('nagoyameshi');
         $admin->save();
 
         $response = $this->actingAs($admin, 'admin')->get(route('admin.categories.index'));
 
         $response->assertStatus(200);
     }
// カテゴリ登録
      // 未ログインのユーザーはカテゴリを登録できない
      public function test_guest_cannot_registration_category()
      {

         $category = Category::factory()->create();
         $registration_category = [
             'name' => 'テスト1',
         ];
 
         $response = $this->post(route('admin.categories.store',$registration_category));
 
         $this->assertDatabaseMissing('categories', $registration_category);
         $response->assertRedirect(route('admin.login'));
      }
  
      // ログイン済みの一般ユーザーはカテゴリを登録できない
      public function test_user_cannot_registration_category()
      {
          $category = Category::factory()->create();
          $user = User::factory()->create();
 
          $registration_category = [
             'name' => 'テスト1',
          ];
          
          $response = $this->actingAs($user)->post(route('admin.categories.store',$registration_category));
 
         $this->assertDatabaseMissing('categories', $registration_category);
         $response->assertRedirect(route('admin.login'));
      }
  
      // ログイン済みの管理者はカテゴリを登録できる
      public function test_admin_can_registration_category()
      {
         $admin = new Admin();
         $admin->email = 'admin@example.com';
         $admin->password = Hash::make('nagoyameshi');
         $admin->save();
          
         $registration_category = [
         'name' => 'テスト',
         ];
          
         $response = $this->actingAs($admin, 'admin')->post(route('admin.categories.store'), $registration_category);
          
         $this->assertDatabaseHas('categories', $registration_category);
         $response->assertRedirect(route('admin.categories.index'));
      }
// カテゴリ更新
       // 未ログインのユーザーはカテゴリを更新できない
     public function test_guest_cannot_update_category()
     {
        $category = Category::factory()->create();

         $update_category = [
           'name' => 'テスト2',
         ];

         $response = $this->patch(route('admin.categories.update', $category),$update_category);
         
         $this->assertDatabaseMissing('categories', $update_category);
         $response->assertRedirect(route('admin.login'));
     }
 
     // ログイン済みの一般ユーザーはカテゴリを更新できない
     public function test_user_cannot_update_category()
     {
         $user = User::factory()->create();
         $category = Category::factory()->create();

         $update_category = [
            'name' => 'テスト2',
         ];

         $response = $this->actingAs($user)->patch(route('admin.categories.update', $category),$update_category);
         
         $this->assertDatabaseMissing('categories', $update_category);
         $response->assertRedirect(route('admin.login'));
     }
 
     // ログイン済みの管理者は店舗を更新できる
        public function test_admin_user_can_update_category()
        {
            $admin = new Admin();
            $admin->email = 'admin@example.com';
            $admin->password = Hash::make('nagoyameshi');
            $admin->save();

            $category = Category::factory()->create();
    
            $update_category= [
                'name' => 'テスト2',
        ];

        $response = $this->actingAs($admin, 'admin')->patch(route('admin.categories.update', $category), $update_category);
       
        $this->assertDatabaseHas('categories', $update_category);
        $response->assertRedirect(route('admin.categories.index'));
    }
          

// カテゴリ削除
     // 未ログインのユーザーはカテゴリを削除できない
     public function test_guest_cannot_destroy_category()
     {
         $user = User::factory()->create();
         $category = Category::factory()->create();
 
         $response = $this->delete(route('admin.categories.destroy', $category));

         $this->assertDatabaseHas('categories', ['id' => $category->id]);
         $response->assertRedirect(route('admin.login'));
     }
 
     // ログイン済みの一般ユーザーはカテゴリを削除できない
     public function test_user_cannot_destoy_category()
     {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        
        $response = $this->actingAs($user)->delete(route('admin.categories.destroy', $category));

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
        $response->assertRedirect(route('admin.login'));
    }
 
     // ログイン済みの管理者はカテゴリを削除できる
     public function test_admin_can_destroy_category()
     {
         $admin = new Admin();
         $admin->email = 'admin@example.com';
         $admin->password = Hash::make('nagoyameshi');
         $admin->save();
 
         $category = Category::factory()->create();
 
         $response = $this->actingAs($admin, 'admin')->delete(route('admin.categories.destroy', $category));
         
         $this->assertDatabaseMissing('categories', ['id' => $category->id]);
         $response->assertRedirect(route('admin.categories.index'));
     }
}

