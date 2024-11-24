<?php

namespace Tests\Feature\Admin;
use App\Models\Admin;
use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


class RestaurantTest extends TestCase
{
    use RefreshDatabase;

    // 店舗一覧 
     // 未ログインのユーザーは管理者側の店舗一覧ページにアクセスできない ●
     public function test_guest_cannot_access_admin_restaurants_index()
     {
         $response = $this->get(route('admin.restaurants.index'));
 
         $response->assertRedirect(route('admin.login'));
     }
 
     // ログイン済みの一般ユーザーは管理者側の店舗一覧ページにアクセスできない　
     public function test_user_cannot_access_admin_restaurants_index()
     {
         $user = User::factory()->create();
 
         $response = $this->actingAs($user)->get(route('admin.restaurants.index'));
 
         $response->assertRedirect(route('admin.login'));
     }
 
     // ログイン済みの管理者は管理者側の店舗一覧ページにアクセスできる
     public function test_admin_can_access_admin_restaurants_index()
     {
         $admin = new Admin();
         $admin->email = 'admin@example.com';
         $admin->password = Hash::make('nagoyameshi');
         $admin->save();
 
         $response = $this->actingAs($admin, 'admin')->get(route('admin.restaurants.index'));
 
         $response->assertStatus(200);
     }
 
    //  店舗詳細　●
     // 未ログインのユーザーは管理者側の店舗詳細ページにアクセスできない
     public function test_guest_cannot_access_admin_restaurants_show()
     {
         $user = User::factory()->create();
         $restaurant = Restaurant::factory()->create();

         $response = $this->get(route('admin.restaurants.show',$restaurant));
 
         $response->assertRedirect(route('admin.login'));
     }
 
     // ログイン済みの一般ユーザーは管理者側の店舗詳細ページにアクセスできない
     public function test_user_cannot_access_admin_restaurants_show()
     {
         $user = User::factory()->create();
         $restaurant = Restaurant::factory()->create();
 
         $response = $this->actingAs($user)->get(route('admin.restaurants.show',$restaurant));
 
         $response->assertRedirect(route('admin.login'));
     }
 
     // ログイン済みの管理者は管理者側の店舗詳細ページにアクセスできる
     public function test_admin_can_access_admin_restaurants_show()
     {
         $admin = new Admin();
         $admin->email = 'admin@example.com';
         $admin->password = Hash::make('nagoyameshi');
         $admin->save();

         $restaurant = Restaurant::factory()->create();
 
         $response = $this->actingAs($admin, 'admin')->get(route('admin.restaurants.show',$restaurant));
 
         $response->assertStatus(200);
     }
    
    // 店舗登録
     // 未ログインのユーザーは管理者側の店舗登録ページにアクセスできない
     public function test_guest_cannot_access_admin_restaurants_create()
     {
         $user = User::factory()->create();
        
         $response = $this->get(route('admin.restaurants.create',$user));
 
         $response->assertRedirect(route('admin.login'));
     }
 
     // ログイン済みの一般ユーザーは管理者側の店舗登録ページにアクセスできない
     public function test_user_cannot_access_admin_restaurants_create()
     {
         $user = User::factory()->create();
 
         $response = $this->actingAs($user)->get(route('admin.restaurants.create'));
 
         $response->assertRedirect(route('admin.login'));
     }
 
     // ログイン済みの管理者は管理者側の店舗登録ページにアクセスできる
     public function test_admin_can_access_admin_restaurants_create()
     {
         $admin = new Admin();
         $admin->email = 'admin@example.com';
         $admin->password = Hash::make('nagoyameshi');
         $admin->save();

 
         $response = $this->actingAs($admin, 'admin')->get(route('admin.restaurants.create'));
 
         $response->assertStatus(200);
     }

// 店舗登録機能
     // 未ログインのユーザーは店舗を登録できない
     public function test_guest_cannot_registration_restaurants()
     {
        $restaurant = Restaurant::factory()->create();
        $registration_restaurant = [
            'name' => 'テスト1',
            'description' => 'テスト',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '0000000',
            'address' => 'テスト',
            'opening_time' => '10:00:00',
            'closing_time' => '20:00:00',
            'seating_capacity' => 50,
          ];

        $response = $this->post(route('admin.restaurants.store',$registration_restaurant));

        $this->assertDatabaseMissing('restaurants', $registration_restaurant);
        $response->assertRedirect(route('admin.login'));
     }
 
     // ログイン済みの一般ユーザーは店舗を登録できない
     public function test_user_cannot_registration_restaurants()
     {
         $restaurant = Restaurant::factory()->create();
         $user = User::factory()->create();

         $registration_restaurant = [
            'name' => 'テスト1',
            'description' => 'テスト',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '0000000',
            'address' => 'テスト',
            'opening_time' => '10:00:00',
            'closing_time' => '20:00:00',
            'seating_capacity' => 50,
         ];
         
         $response = $this->actingAs($user)->post(route('admin.restaurants.store',$registration_restaurant));

        $this->assertDatabaseMissing('restaurants', $registration_restaurant);
        $response->assertRedirect(route('admin.login'));
     }
 
     // ログイン済みの管理者は店舗を登録できる
     public function test_admin_can_registration_restaurants()
     {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
         
        $restaurant_data = [
        'name' => 'テスト',
        'description' => 'テスト',
        'lowest_price' => 1000,
        'highest_price' => 10000,
        'postal_code' => '0000000',
        'address' => 'テスト',
        'opening_time' => '10:00:00',
        'closing_time' => '20:00:00',
        'seating_capacity' => 1
        ];
         
        $response = $this->actingAs($admin, 'admin')->post(route('admin.restaurants.store'), $restaurant_data);
         
        $this->assertDatabaseHas('restaurants', $restaurant_data);
        $response->assertRedirect(route('admin.restaurants.index'));
        
     }

    // 店舗編集
     // 未ログインのユーザーは管理者側の店舗編集ページにアクセスできない
     public function test_guest_cannot_access_admin_restaurants_edit()
     {
         $user = User::factory()->create();

         $response = $this->get(route('admin.restaurants.edit', $user));
 
         $response->assertRedirect(route('admin.login'));
     }
 
     // ログイン済みの一般ユーザーは管理者側の店舗編集ページにアクセスできない
     public function test_user_cannot_access_admin_restaurants_edit()
     {
         $user = User::factory()->create();
 
         $response = $this->actingAs($user)->get(route('admin.restaurants.edit',$user));
 
         $response->assertRedirect(route('admin.login'));
     }
 
     // ログイン済みの管理者は管理者側の店舗編集ページにアクセスできる
     public function test_admin_can_access_admin_restaurants_edit()
     {
         $admin = new Admin();
         $admin->email = 'admin@example.com';
         $admin->password = Hash::make('nagoyameshi');
         $admin->save();

         $restaurant = Restaurant::factory()->create();
 
         $response = $this->actingAs($admin, 'admin')->get(route('admin.restaurants.edit',$restaurant));
 
         $response->assertStatus(200);
     }


// 店舗更新機能
     // 未ログインのユーザーは店舗を更新できない
     public function test_guest_cannot_update_restaurans()
     {
        $restaurant = Restaurant::factory()->create();

         $update_restaurant = [
           'name' => 'テスト2',
           'description' => 'テスト',
           'lowest_price' => 1000,
           'highest_price' => 5000,
           'postal_code' => '0000000',
           'address' => 'テスト',
           'opening_time' => '10:00:00',
           'closing_time' => '20:00:00',
           'seating_capacity' => 50,
         ];

         $response = $this->patch(route('admin.restaurants.update', $restaurant),$update_restaurant);
         
         $this->assertDatabaseMissing('restaurants', $update_restaurant);
         $response->assertRedirect(route('admin.login'));
     }
 
     // ログイン済みの一般ユーザーは店舗を更新できない
     public function test_user_cannot_update_restaurants()
     {
         $user = User::factory()->create();
         $restaurant = Restaurant::factory()->create();

         $update_restaurant = [
            'name' => 'テスト2',
            'description' => 'テスト',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '0000000',
            'address' => 'テスト',
            'opening_time' => '10:00:00',
            'closing_time' => '20:00:00',
            'seating_capacity' => 50,
         ];


         $response = $this->actingAs($user)->patch(route('admin.restaurants.update', $restaurant),$update_restaurant);
         
         $this->assertDatabaseMissing('restaurants', $update_restaurant);
         $response->assertRedirect(route('admin.login'));
     }
 
     // ログイン済みの管理者は店舗を更新できる
        public function test_admin_user_can_update_restaurant()
        {
            $admin = new Admin();
            $admin->email = 'admin@example.com';
            $admin->password = Hash::make('nagoyameshi');
            $admin->save();

            $restaurant = Restaurant::factory()->create();
    
            $update_restaurant_data = [
                'name' => 'Updated Store Name',
                'description' => 'A brief description of the store.',
                'lowest_price' => 1000,
                'highest_price' => 5000,
                'postal_code' => '1234567',
                'address' => '123 Main St',
                'opening_time' => '10:00',
                'closing_time' => '20:00',
                'seating_capacity' => 50,
        ];

        $response = $this->actingAs($admin, 'admin')->patch(route('admin.restaurants.update', $restaurant), $update_restaurant_data);
       
        $this->assertDatabaseHas('restaurants', $update_restaurant_data);
        $response->assertRedirect(route('admin.restaurants.show',$restaurant));
    }
          

     // 店舗削除
     // 未ログインのユーザーは店舗を削除できない
     public function test_guest_cannot_destroy_restaurant()
     {
         $restaurant = Restaurant::factory()->create();
         $restaurantData = $restaurant->toArray();

         $restaurantData['lowest_price'] = (int) $restaurantData['lowest_price'];
         $restaurantData['highest_price'] = (int) $restaurantData['highest_price'];
         $restaurantData['seating_capacity'] = (int) $restaurantData['seating_capacity'];
 
         unset($restaurantData['created_at'], $restaurantData['updated_at']);
 
         $response = $this->delete(route('admin.restaurants.destroy', $restaurant));
         $this->assertDatabaseHas('restaurants', $restaurantData);
         $response->assertRedirect(route('admin.login'));
     }
 
     // ログイン済みの一般ユーザーは店舗を削除できない
     public function test_user_cannot_destoy_restaurants()
     {
         $user = User::factory()->create();
         $restaurant = Restaurant::factory()->create();
         $restaurantData = $restaurant->toArray();

        $restaurantData['lowest_price'] = (int) $restaurantData['lowest_price'];
        $restaurantData['highest_price'] = (int) $restaurantData['highest_price'];
        $restaurantData['seating_capacity'] = (int) $restaurantData['seating_capacity'];

        unset($restaurantData['created_at'], $restaurantData['updated_at']);

        $response = $this->actingAs($user, 'web')->delete(route('admin.restaurants.destroy', $restaurant));
        $this->assertDatabaseHas('restaurants', $restaurantData);
        $response->assertRedirect(route('admin.login'));
    }
 
     // ログイン済みの管理者は店舗を削除できる
     public function test_admin_can_destroy_restaurants()
     {
         $admin = new Admin();
         $admin->email = 'admin@example.com';
         $admin->password = Hash::make('nagoyameshi');
         $admin->save();
 
         $restaurant = Restaurant::factory()->create();
 
         $response = $this->actingAs($admin, 'admin')->delete(route('admin.restaurants.destroy', $restaurant));
         $this->assertDatabaseMissing('restaurants', $restaurant->toArray());
         $response->assertRedirect(route('admin.restaurants.index'));
     }
}
