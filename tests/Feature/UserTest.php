<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


class UserTest extends TestCase
{
    use RefreshDatabase;

// 会社情報ページ
    // 未ログインのユーザーは会員側の会員情報ページにアクセスできない
    public function test_guest_cannot_access_user_information_index()
     {
         $response = $this->get(route('user.index'));
 
         $response->assertRedirect(route('login'));
     }
 
     // ログイン済みの一般ユーザーは会員側の会員情報ページにアクセスできる
     public function test_user_can_access_user_information_index()
     {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('user.index'));

        $response->assertStatus(200);
     }
 
     // ログイン済みの管理者は会員側の会員情報ページにアクセスできない
     public function test_admin_can_access_user_information_index()
     {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $response = $this->actingAs($admin, 'admin')->get(route('user.index'));

        $response->assertRedirect(route('admin.home'));
     }
 
// 会員情報編集ページ
     // 未ログインのユーザーは会員側の会員情報編集ページにアクセスできない
     public function test_guest_cannot_access_user_information_edit()
     {
         $user = User::factory()->create();

         $response = $this->get(route('user.edit', $user));
 
         $response->assertRedirect(route('login'));
     }
 
     // ログイン済みの一般ユーザーは会員側の他人の会員情報編集ページにアクセスできない
     public function test_user_cannot_access_user_others_information_edit()
     {
         $user = User::factory()->create();
         $other_user = User::factory()->create();
         
         $response = $this->actingAs($user)->get(route('user.edit',$other_user->id));
 
         $response->assertRedirect(route('user.index'));
     }


    // ログイン済みの一般ユーザーは会員側の自身の会員情報編集ページにアクセスできる
    public function test_user_can_access_user_own_information_edit()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('user.edit', $user->id));

        $response->assertStatus(200);
    }

    // ログイン済みの管理者は会員側の会員情報編集ページにアクセスできない
    public function test_admin_can_access_user_information_edit()
    {
       $user = User::factory()->create();
    
       $admin = new Admin();
       $admin->email = 'admin@example.com';
       $admin->password = Hash::make('nagoyameshi');
       $admin->save();

       $response = $this->actingAs($admin, 'admin')->get(route('user.edit', $user));

       $response->assertRedirect(route('admin.home'));
    }

// 会員情報更新機能
       // 未ログインのユーザーは会員情報を更新できない
     public function test_guest_cannot_update_information()
     {
        $user = User::factory()->create();

         $update_user = [
            'name' => '花子',
            'kana' => 'ハナコ',
            'email' => 'hanako@example.com',
            'postal_code' => '1112223',
            'address' => '北海道',
            'phone_number' => '1234567891',
            'birthday' => '20000123',
            'occupation' =>'会社員'
         ];

         $response = $this->patch(route('user.update', $user->id),$update_user);
         
         $this->assertDatabaseMissing('users', $update_user);
         $response->assertRedirect(route('login'));
     }
 
     // ログイン済みの一般ユーザーは他人の会員情報を更新できない
     public function test_user_cannot_update_others_information()
     {
         $user = User::factory()->create();
         $other_user = User::factory()->create();

         $update_user = [
            'name' => '花子',
            'kana' => 'ハナコ',
            'email' => 'hanako@example.com',
            'postal_code' => '1112223',
            'address' => '北海道',
            'phone_number' => '1234567891',
            'birthday' => '20000123',
            'occupation' =>'会社員'
         ];

         $response = $this->actingAs($user)->patch(route('user.update', $other_user->id),$update_user);

         $response->assertRedirect(route('user.index'));
     }

     // ログイン済みの一般ユーザーは自身の会員情報を更新できる
     public function test_user_can_update_own_information()
     {
         $user = User::factory()->create();

         $update_user = [
            'name' => '花子',
            'kana' => 'ハナコ',
            'email' => 'hanako@example.com',
            'postal_code' => '1112223',
            'address' => '北海道',
            'phone_number' => '1234567891',
            'birthday' => '20000123',
            'occupation' =>'会社員'
         ];

         $response = $this->actingAs($user)->patch(route('user.update', $user->id),$update_user);

         $this->assertDatabaseHas('users', $update_user);

     }
 
     // ログイン済みの管理者は会員情報を更新できない
        public function test_admin_user_cannot_update_information()
        {
            $admin = new Admin();
            $admin->email = 'admin@example.com';
            $admin->password = Hash::make('nagoyameshi');
            $admin->save();

            $user = User::factory()->create();

            $update_user = [
             'name' => '花子',
             'kana' => 'ハナコ',
             'email' => 'hanako@example.com',
             'postal_code' => '1112223',
             'address' => '北海道',
             'phone_number' => '1234567891',
             'birthday' => '20000123',
             'occupation' =>'会社員'
            ];

            $response = $this->actingAs($admin, 'admin')->patch(route('user.update', $user->id),$update_user);
         
            $response->assertRedirect(route('admin.home'));

            $this->assertDatabaseMissing('users', $update_user);

        }

 }