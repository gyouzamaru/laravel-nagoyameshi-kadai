<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\user;
use App\Models\Restaurant;
use App\Models\Review;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

// レビュ一覧ページ
 // 未ログインのユーザーは会員側のレビュー一覧ページにアクセスできない
  public function test_guest_cannot_access_user_review_index()
  {
    $restaurant = Restaurant::factory()->create();

    $response = $this->get(route('restaurants.reviews.index', $restaurant));

    $response->assertRedirect(route('login'));
  }
 // ログイン済みの無料会員は会員側のレビュー一覧ページにアクセスできる
  public function test_freeuser_can_access_user_review_index()
  {
    $user = User::factory()->create();

    $restaurant = Restaurant::factory()->create();

    $response = $this->actingAs($user)->get(route('restaurants.reviews.index', $restaurant));

    $response->assertStatus(200);
  }
 // ログイン済みの有料会員は会員側のレビュー一覧ページにアクセスできる
  public function test_paiduser_can_access_user_review_index()
  {
    $user = User::factory()->create();
    $user->newSubscription('premium_plan', 'price_1QTLrRJwj3ULRs8ZvoC1jlpj')->create('pm_card_visa');

    $restaurant = Restaurant::factory()->create();
    $response = $this->actingAs($user)->get(route('restaurants.reviews.index', $restaurant));

    $response->assertStatus(200);
  }
 // ログイン済みの管理者は会員側のレビュー一覧ページにアクセスできない
  public function test_admin_cannot_access_user_review_index()
  {
    $admin = new Admin();
    $admin->email = 'admin@example.com';
    $admin->password = Hash::make('nagoyameshi');
    $admin->save();

    $restaurant = Restaurant::factory()->create();

    $response = $this->actingAs($admin, 'admin')->get(route('restaurants.reviews.index', $restaurant));

    $response->assertRedirect(route('admin.home'));
  }

// レビュー投稿ページ
  //未ログインのユーザーは会員側のレビュー投稿ページにアクセスできない
  public function test_guest_cannot_access_user_review_create()
  {
    $restaurant = Restaurant::factory()->create();

    $response = $this->get(route('restaurants.reviews.create',  $restaurant));

    $response->assertRedirect(route('login'));
  }
  // ログイン済みの無料会員は会員側のレビュー投稿ページにアクセスできない
   public function test_freeuser_cannot_access_user_review_create()
   {
     $user = User::factory()->create();
     $restaurant = Restaurant::factory()->create();
 
     $response = $this->actingAs($user)->get(route('restaurants.reviews.create', $restaurant));
 
     $response->assertRedirect(route('subscription.create'));
   }
  //  ログイン済みの有料会員は会員側のレビュー投稿ページにアクセスできる
   public function test_paiduser_can_access_user_review_create()
   {
    $user = User::factory()->create();
    $user->newSubscription('premium_plan', 'price_1QTLrRJwj3ULRs8ZvoC1jlpj')->create('pm_card_visa');

    $restaurant = Restaurant::factory()->create();

    $response = $this->actingAs($user)->get(route('restaurants.reviews.create', $restaurant));

    $response->assertStatus(200);
  }
  // ログイン済みの管理者は会員側のレビュー投稿ページにアクセスできない
  public function test_admin_cannot_access_user_review_create()
  {
    $admin = new Admin();
    $admin->email = 'admin@example.com';
    $admin->password = Hash::make('nagoyameshi');
    $admin->save();

    $restaurant = Restaurant::factory()->create();

    $response = $this->actingAs($admin, 'admin')->get(route('restaurants.reviews.create', $restaurant));

    $response->assertRedirect(route('admin.home'));
  }

// レビュー投稿機能
  // 未ログインのユーザーはレビューを投稿できない
  public function test_guest_cannot_store_review()
  {
    $restaurant = Restaurant::factory()->create();

    $response = $this->post(route('restaurants.reviews.store', $restaurant), [
        'score' => 5,
        'content' => 'テストレビュー',
    ]);

    $response->assertRedirect(route('login'));
}
   // ログイン済みの無料会員はレビューを投稿できない
    public function test_freeuser_cannot_store_review()
    {
        $user = User::factory()->create();

        $restaurant = Restaurant::factory()->create();
        
        $response = $this->actingAs($user)->post(route('restaurants.reviews.store', $restaurant),[
            'score' => 5,
            'content' => 'テストレビュー',
        ]);

        $response->assertRedirect('subscription/create');
    }
   // ログイン済みの有料会員はレビューを投稿できる
   public function test_paiduser_can_store_review()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QTLrRJwj3ULRs8ZvoC1jlpj')->create('pm_card_visa');
        
        $restaurant = Restaurant::factory()->create();

        $response = $this->actingAs($user)->post(route('restaurants.reviews.store', $restaurant),[
            'score' => 5,
            'content' => 'テストレビュー',
        ]);

        $response->assertRedirect(route('restaurants.reviews.index', $restaurant));
    }
// ログイン済みの管理者はレビューを投稿できない
    public function test_admin_user_cannot_store_review()
    {
       $admin = new Admin();
       $admin->email = 'admin@example.com';
       $admin->password = Hash::make('nagoyameshi');
       $admin->save();
        
       $restaurant = Restaurant::factory()->create();

       $review_data = [
        'score' => 5,
        'content' => 'テストレビュー'
       ];

       $response = $this->actingAs($admin, 'admin')->post(route('restaurants.reviews.store', $restaurant), $review_data);
       $this->assertDatabaseMissing('reviews', $review_data);
       $response->assertRedirect(route('admin.home'));
    }

// レビュー編集ページ
// 未ログインのユーザーは会員側のレビュー編集ページにアクセスできない
    public function test_guest_cannot_access_user_review_edit()
    {
      $user = User::factory()->create();

      $restaurant = Restaurant::factory()->create();
      $review = Review::factory()->create(['restaurant_id' => $restaurant->id, 'user_id' => $user->id,]);

      $response = $this->get(route('restaurants.reviews.edit',[$restaurant, $review]));
 
      $response->assertRedirect(route('login'));
    }
   // ログイン済みの無料会員はレビュー編集ページにアクセスできない
   public function test_freeuser_cannot_access_user_review_edit()
   {
      $user = User::factory()->create();

      $restaurant = Restaurant::factory()->create();
     
      $review = Review::factory()->create(['restaurant_id' => $restaurant->id, 'user_id' => $user->id, ]);

      $response = $this->actingAs($user)->get(route('restaurants.reviews.edit',[$restaurant, $review]));

      $response->assertRedirect(route('subscription.create'));
   }
  // ログイン済みの有料会員は会員側の他人のレビュー編集ページにアクセスできない
  public function test_paiduser_cannot_access_user_others_review_edit()
  {
     $user = User::factory()->create();
     $user->newSubscription('premium_plan', 'price_1QTLrRJwj3ULRs8ZvoC1jlpj')->create('pm_card_visa');

     $restaurant = Restaurant::factory()->create();
     $otherUser = User::factory()->create();
     $review = Review::factory()->create(['restaurant_id' => $restaurant->id, 'user_id' => $otherUser->id,]);

      $response = $this->actingAs($user)->get(route('restaurants.reviews.edit', [$restaurant, $review]));

      $response->assertRedirect(route('restaurants.reviews.index',  $restaurant));
    }
   // ログイン済みの有料会員は会員側の自身のレビュー編集ページにアクセスできる
   public function test_paiduser_can_access_user_own_review_edit()
   {
     $user = User::factory()->create();
     $user->newSubscription('premium_plan', 'price_1QTLrRJwj3ULRs8ZvoC1jlpj')->create('pm_card_visa');
  
     $restaurant = Restaurant::factory()->create();
     $review = Review::factory()->create(['restaurant_id' => $restaurant->id, 'user_id' => $user->id]);

     $response = $this->actingAs($user)->get(route('restaurants.reviews.edit',[$restaurant, $review]));
  
     $response->assertStatus(200);
  }
  
 // ログイン済みの管理者は会員側のレビュー編集ページにアクセスできない
public function test_admin_cannot_access_user_review_edit()
{
    $admin = new Admin();
    $admin->email = 'admin@example.com';
    $admin->password = Hash::make('nagoyameshi');
    $admin->save();
  
    $user = User::factory()->create();
    $restaurant = Restaurant::factory()->create();
    $review = Review::factory()->create(['restaurant_id' => $restaurant->id, 'user_id' => $user->id]);

    $response = $this->actingAs($admin, 'admin')->get(route('restaurants.reviews.edit', [$restaurant, $review]));

    $response->assertRedirect(route('admin.home'));
}
  
// レビュー更新機能
  // 未ログインのユーザーはレビューを更新できない
  public function test_guest_cannot_update_review()
  {
     $restaurant = Restaurant::factory()->create();
     $user = User::factory()->create();
     $review = Review::factory()->create(['restaurant_id' => $restaurant->id, 'user_id' => $user->id]);

     $update_review_data = [
      'score' => 5,
      'content' => 'テスト更新'
     ];

     $response = $this->patch(route('restaurants.reviews.update', [$restaurant, $review]), $update_review_data); 
     $this->assertDatabaseMissing('reviews', $update_review_data);
     $response->assertRedirect(route('login'));
  }

  // ログイン済みの無料会員はレビューを更新できない
  public function test_freeuser_cannot_update_review()
  {
     $user = User::factory()->create();
     $restaurant = Restaurant::factory()->create();
     $review = Review::factory()->create(['restaurant_id' => $restaurant->id, 'user_id' => $user->id]);

     $update_review_data = [
      'score' => 5,
      'content' => 'テスト更新',
     ];

     $response = $this->actingAs($user)->patch(route('restaurants.reviews.update', [$restaurant, $review]), $update_review_data); 
     $this->assertDatabaseMissing('reviews', $update_review_data);
     $response->assertRedirect(route('subscription.create'));
  }

  // ログイン済みの有料会員は他人のレビューを更新できない
  public function test_paiduser_cannot_update_others_review()
  {
     $user = User::factory()->create();
     $user->newSubscription('premium_plan', 'price_1QTLrRJwj3ULRs8ZvoC1jlpj')->create('pm_card_visa');
    
     $restaurant = Restaurant::factory()->create();

     $otherUser = User::factory()->create();
     $review = Review::factory()->create(['restaurant_id' => $restaurant->id, 'user_id' => $otherUser->id]);

     $response = $this->actingAs($user)->put(route('restaurants.reviews.update', [$restaurant, $review]), [
        'score' => 5,
        'content' => 'テスト更新',
     ]);

    $response->assertRedirect(route('restaurants.reviews.index', $restaurant));
  }

  // ログイン済みの有料会員は自身のレビューを更新できる
 public function test_paiduser_can_update_own_review()
 {
     $user = User::factory()->create();
     $user->newSubscription('premium_plan', 'price_1QTLrRJwj3ULRs8ZvoC1jlpj')->create('pm_card_visa');

     $restaurant = Restaurant::factory()->create();

     $review = Review::factory()->create(['restaurant_id' => $restaurant->id, 'user_id' => $user->id]);

     $response = $this->actingAs($user)->put(route('restaurants.reviews.update', [$restaurant, $review]), [
        'score' => 5,
        'content' => 'テスト更新',
     ]);

     $response->assertRedirect(route('restaurants.reviews.index', $restaurant));
    
     $review->refresh();
     $this->assertEquals('テスト更新', $review->content);
  }

  // ログイン済みの管理者はレビューを更新できない
  public function test_admin_cannot_update_review()
  {
     $admin = new Admin();
     $admin->email = 'admin@example.com';
     $admin->password = Hash::make('nagoyameshi');
     $admin->save();
  
     $user = User::factory()->create();
     $restaurant = Restaurant::factory()->create();
     $review = Review::factory()->create(['restaurant_id' => $restaurant->id, 'user_id' =>  $user->id]);

     $update_review_data = [
      'score' => 5,
      'content' => 'テスト更新',
     ];

     $response = $this->actingAs($admin, 'admin')->patch(route('restaurants.reviews.update', [$restaurant, $review]), $update_review_data); 
     $this->assertDatabaseMissing('reviews', $update_review_data);
     $response->assertRedirect(route('admin.home'));
  }

// レビュー削除機能
  // 未ログインのユーザーはレビューを削除できない
  public function test_guest_cannot_delete_review()
  {
      $restaurant = Restaurant::factory()->create();
      $user = User::factory()->create();
      $review = Review::factory()->create(['restaurant_id' => $restaurant->id,'user_id' => $user->id]);

      $response = $this->delete(route('restaurants.reviews.destroy', [$restaurant, $review]));
      $this->assertDatabaseHas('reviews', ['id' => $review->id]);
  }

  // ログイン済みの無料会員はレビューを削除できない
  public function test_freeuser_cannot_delete_review()
  {
      $user = User::factory()->create();
      $restaurant = Restaurant::factory()->create();
      $review = Review::factory()->create(['restaurant_id' => $restaurant->id, 'user_id' =>$user->id]);

      $response = $this->actingAs($user)->delete(route('restaurants.reviews.destroy', [$restaurant, $review]));
      $this->assertDatabaseHas('reviews', ['id' => $review->id]);
      $response->assertRedirect(route('subscription.create'));
  }

  // ログイン済みの有料会員は他人のレビューを削除できない
  public function test_paiduser_cannot_delete_others_review()
  {
      $user = User::factory()->create();
      $user->newSubscription('premium_plan', 'price_1QTLrRJwj3ULRs8ZvoC1jlpj')->create('pm_card_visa');

      $other_user = User::factory()->create();
      $restaurant = Restaurant::factory()->create();
      $review = Review::factory()->create(['restaurant_id' => $restaurant->id, 'user_id'=> $other_user->id]);

      $response = $this->actingAs($user)->delete(route('restaurants.reviews.destroy', [$restaurant, $review]));

      $this->assertDatabaseHas('reviews', ['id' => $review->id]);
  
      $response->assertRedirect(route('restaurants.reviews.index', [$restaurant]));
  }

  // ログイン済みの有料会員は自身のレビューを削除できる
  public function test_paiduser_can_delete_own_review()
  {
      $user = User::factory()->create();
      $user->newSubscription('premium_plan', 'price_1QTLrRJwj3ULRs8ZvoC1jlpj')->create('pm_card_visa');

      $restaurant = Restaurant::factory()->create();
      $review = Review::factory()->create(['restaurant_id' => $restaurant->id, 'user_id' => $user->id]);

      $response = $this->actingAs($user)->delete(route('restaurants.reviews.destroy', [$restaurant, $review]));

      $response->assertRedirect(route('restaurants.reviews.index', $restaurant));
      $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
  }

  // ログイン済みの管理者はレビューを削除できない
  public function test_admin_cannot_delete_review()
  {
      $admin = new Admin();
      $admin->email = 'admin@example.com';
      $admin->password = Hash::make('nagoyameshi');
      $admin->save();

      $user = User::factory()->create();
      $restaurant = Restaurant::factory()->create();
      $review = Review::factory()->create(['restaurant_id' => $restaurant->id, 'user_id' => $user->id]);

    
      $response = $this->actingAs($admin, 'admin')->delete(route('restaurants.reviews.destroy', [$restaurant, $review]));
      $this->assertDatabaseHas('reviews', ['id' => $review->id]);
      $response->assertRedirect(route('admin.home'));
  }
}
