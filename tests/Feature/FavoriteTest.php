<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\user;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

// お気に入り一覧ページ
   // 未ログインのユーザーは会員側のお気に入り一覧ページにアクセスできない
   public function test_guest_cannot_access_user_favorite_index()
   {
        $response = $this->get(route('favorites.index'));

        $response->assertRedirect(route('login'));
   }
   // ログイン済みの無料会員は会員側のお気に入り一覧ページにアクセスできない
   public function test_freeuser_cannot_access_user_favorite_index()
   {
        $user = User::factory()->create();
 
        $response = $this->actingAs($user)->get(route('favorites.index'));
 
        $response->assertRedirect(route('subscription.create'));
   }
   // ログイン済みの有料会員は会員側のお気に入り一覧ページにアクセスできる
   public function test_paiduser_can_access_user_favorite_index()
   {
       $user = User::factory()->create();
       $user->newSubscription('premium_plan', 'price_1QTLrRJwj3ULRs8ZvoC1jlpj')->create('pm_card_visa');

       $response = $this->actingAs($user)->get(route('favorites.index'));

       $response->assertStatus(200);
    }
   // ログイン済みの管理者は会員側のお気に入り一覧ページにアクセスできない
   public function test_admin_cannot_access_user_favorite_index()
   {
      $admin = new Admin();
      $admin->email = 'admin@example.com';
      $admin->password = Hash::make('nagoyameshi');
      $admin->save();

      $response = $this->actingAs($admin, 'admin')->get(route('favorites.index'));

      $response->assertRedirect(route('admin.home'));
   }

// お気に入り追加機能
   // 未ログインのユーザーはお気に入りに追加できない
   public function test_guest_cannot_add_it_to_my_favorite()
   {
      $restaurant = Restaurant::factory()->create();
      $response = $this->post(route('favorites.store', $restaurant->id));

      $response->assertRedirect(route('login'));
   }
   // ログイン済みの無料会員はお気に入りに追加できない
   public function test_freeuser_cannot_add_it_to_my_favorite()
   {
      $restaurant = Restaurant::factory()->create();
      $user = User::factory()->create();
      $response = $this->actingAs($user)->post(route('favorites.store', $restaurant->id));

      $response->assertRedirect(route('subscription.create'));
   }
   // ログイン済みの有料会員はお気に入りに追加できる
   public function test_paiduser_can_add_it_to_my_favorite()
   {
      $restaurant = Restaurant::factory()->create();
      $user = User::factory()->create();
      $user->newSubscription('premium_plan', 'price_1QTLrRJwj3ULRs8ZvoC1jlpj')->create('pm_card_visa');

      $response = $this->actingAs($user)->post(route('favorites.store', $restaurant->id));

      $response->assertStatus(302);
   }
   // ログイン済みの管理者はお気に入りに追加できない
   public function test_admin_cannot_add_it_to_my_favorite()
   {
      $admin = new Admin();
      $admin->email = 'admin@example.com';
      $admin->password = Hash::make('nagoyameshi');
      $admin->save();

      $restaurant = Restaurant::factory()->create();
   
      $response = $this->actingAs($admin, 'admin')->post(route('favorites.store', $restaurant->id));

      $response->assertRedirect(route('admin.home'));
   }


// お気に入り解除機能
   // 未ログインのユーザーはお気に入りを解除できない
   public function test_user_cannot_remove_favorite()
   {
      $restaurant = Restaurant::factory()->create();
    
      $response = $this->post(route('favorites.destroy', $restaurant->id));
    
      $response->assertRedirect(route('login'));
   }

   // ログイン済みの無料会員はお気に入りを解除できない
   public function test_freeuser_cannot_remove_favorite()
   {
      $restaurant = Restaurant::factory()->create();
      $user = User::factory()->create();
      $response = $this->actingAs($user)->post(route('favorites.destroy', $restaurant->id));

      $response->assertRedirect(route('subscription.create'));
   }
   // ログイン済みの有料会員はお気に入りを解除できる
   public function test_paiduser_can_remove_favorite()
   {
      $restaurant = Restaurant::factory()->create();
      $user = User::factory()->create();
      $user->newSubscription('premium_plan', 'price_1QTLrRJwj3ULRs8ZvoC1jlpj')->create('pm_card_visa');

      $response = $this->actingAs($user)->post(route('favorites.destroy', $restaurant->id));

      $response->assertStatus(302);
   }
   // ログイン済みの管理者はお気に入りを解除できない
   public function test_admin_cannot_remove_favorite()
   {
      $admin = new Admin();
      $admin->email = 'admin@example.com';
      $admin->password = Hash::make('nagoyameshi');
      $admin->save();

      $restaurant = Restaurant::factory()->create();
   
      $response = $this->actingAs($admin, 'admin')->post(route('favorites.destroy', $restaurant->id));

      $response->assertRedirect(route('admin.home'));
   }
}
