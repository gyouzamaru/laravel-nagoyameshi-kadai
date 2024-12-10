<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\user;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;
    
// 有料プラン登録ページ
    // 未ログインのユーザーは有料プラン登録ページにアクセスできない
    public function test_guest_cannot_access_paidplans_create()
     {
         $response = $this->get(route('subscription.create'));
 
         $response->assertRedirect(route('login'));
     }
 
     // ログイン済みの無料会員は有料プラン登録ページにアクセスできる
     public function test_freeuser_can_access_paidplans_create()
     {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('subscription.create'));

        $response->assertStatus(200);
     }

     // ログイン済みの有料会員は有料プラン登録ページにアクセスできない
     public function test_paiduser_cannot_access_paidplans_create()
     {
        $user = User::factory()->create();
        
        $user->newSubscription('premium_plan', 'price_1QTLrRJwj3ULRs8ZvoC1jlpj')->create('pm_card_visa');

        $response = $this->actingAs($user)->get(route('subscription.create'));

        $response->assertRedirect(route('subscription.edit'));
     }
 
     // ログイン済みの管理者は有料プラン登録ページにアクセスできない
     public function test_admin_cannot_access_paidplans_create()
     {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $response = $this->actingAs($admin, 'admin')->get(route('subscription.create'));

        $response->assertRedirect(route('admin.home'));
     }


// 有料プラン登録機能
    // 未ログインのユーザーは有料プランに登録できない
    public function test_guest_cannot_registration_paidplans_store()
     {
        $request_parameter = ['paymentMethodId' => 'pm_card_visa'];
        
        $response = $this->post(route('subscription.store',$request_parameter));
 
        $response->assertRedirect(route('login'));
     }
 
     // ログイン済みの無料会員は有料プランに登録できる
     public function test_freeuser_can_registration_paidplans_store()
     {
        $user = User::factory()->create();

        $request_parameter = ['paymentMethodId' => 'pm_card_visa'];

        $response = $this->actingAs($user)->post(route('subscription.store',$request_parameter));
        
        $response->assertStatus(302); 
        $response->assertSessionHas('flash_message', '有料プランへの登録が完了しました。');
    
        $this->assertTrue($user->fresh()->subscribed('premium_plan'));
     }

     // ログイン済みの有料会員は有料プランに登録できない
     public function test_paiduser_cannot_registration_paidplans_store()
     {

        $request_parameter = ['paymentMethodId' => 'pm_card_visa'];

        $user = User::factory()->create();
        
        $user->newSubscription('premium_plan', 'price_1QTLrRJwj3ULRs8ZvoC1jlpj')->create('pm_card_visa');

        $response = $this->actingAs($user)->post(route('subscription.store',$request_parameter));

        $response->assertRedirect(route('subscription.edit'));
     }
 
     // ログイン済みの管理者は有料プランの登録できない
     public function test_admin_cannot_registration_paidplans_store()
     {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $request_parameter = ['paymentMethodId' => 'pm_card_visa'];

        $response = $this->actingAs($admin, 'admin')->post(route('subscription.store',$request_parameter));

        $response->assertRedirect(route('admin.home'));
     }


// お支払方法編集ページ
    // 未ログインのユーザーはお支払方法編集ページにアクセスできない
    public function test_guest_cannot_access_payment_method_edit()
     {
         $response = $this->get(route('subscription.edit'));
 
         $response->assertRedirect(route('login'));
     }
 
     // ログイン済みの無料会員はお支払方法編集ページにアクセスできない
     public function test_freeuser_cannot_access_payment_method_edit()
     {              
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('subscription.edit'));

        $response->assertRedirect(route('subscription.create'));
     }

     // ログイン済みの有料会員はお支払方法編集ページにアクセスできる
     public function test_paiduser_can_access_payment_method_edit()
     {
        $user = User::factory()->create();
        
        $user->newSubscription('premium_plan', 'price_1QTLrRJwj3ULRs8ZvoC1jlpj')->create('pm_card_visa');

        $response = $this->actingAs($user)->get(route('subscription.edit'));

        $response->assertStatus(200);
     }
 
     // ログイン済みの管理者はお支払方法編集ページにアクセスできない
     public function test_admin_cannot_access_payment_method_edit()
     {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $response = $this->actingAs($admin, 'admin')->get(route('subscription.edit'));

        $response->assertRedirect(route('admin.home'));
     }

//お支払方法更新機能
    // 未ログインのユーザーはお支払方法を更新できない
    public function test_guest_cannot_update_payment_method()
    {
        $request_parameter = ['paymentMethodId' => 'pm_card_mastercard'];        
  
        $response = $this->patch(route('subscription.update'),$request_parameter);

        $response->assertRedirect(route('login'));
    }
   
    // ログイン済みの無料会員はお支払方法を更新できない
    public function test_freeuser_cannot_update_payment_method()
    {
         $user = User::factory()->create();
        
         $request_parameter = ['paymentMethodId' => 'pm_card_mastercard'];        
  
         $response = $this->actingAs($user)->patch(route('subscription.update'),$request_parameter);
           
         $response->assertRedirect(route('subscription.create'));
    }

    // ログイン済みの有料会員はお支払方法を更新できる
    public function test_paiduser_can_update_payment_method()
    {

        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QTLrRJwj3ULRs8ZvoC1jlpj')->create('pm_card_visa');

        $default_payment_method_id = $user->defaultPaymentMethod()->id;
        
        $request_parameter = ['paymentMethodId' => 'pm_card_mastercard'];
           
        $response = $this->actingAs($user)->patch(route('subscription.update'),$request_parameter);
        $response->assertRedirect(route('home'));

        $this->assertNotEquals($default_payment_method_id, $user->defaultPaymentMethod()->id);
    }
   
    // ログイン済みの管理者はお支払方法を更新できない
    public function test_admin_user_can_update_payment_method()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
  
        $request_parameter = ['paymentMethodId' => 'pm_card_mastercard'];
  
        $response = $this->actingAs($admin, 'admin')->patch(route('subscription.update'), $request_parameter);
         
        $response->assertRedirect(route('admin.home'));
    }

// 有料プラン解約ページ
    // 未ログインのユーザーは有料プラン解約ページにアクセスできない
    public function test_guest_cannot_access_paid_plan_cancel()
     {
         $response = $this->get(route('subscription.cancel'));
 
         $response->assertRedirect(route('login'));
     }
 
     // ログイン済みの無料会員は有料プラン解約ページにアクセスできない
     public function test_freeuser_cannot_access_paid_plan_cancel()
     {              
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('subscription.cancel'));

        $response->assertRedirect(route('subscription.create'));
     }

     // ログイン済みの有料会員は有料プラン解約ページにアクセスできる
     public function test_paiduser_can_access_paid_plan_cancel()
     {
        $user = User::factory()->create();
        
        $user->newSubscription('premium_plan', 'price_1QTLrRJwj3ULRs8ZvoC1jlpj')->create('pm_card_visa');

        $response = $this->actingAs($user)->get(route('subscription.cancel'));

        $response->assertStatus(200);
     }
 
     // ログイン済みの管理者は有料プラン解約ページにアクセスできない
     public function test_admin_cannot_access_paid_plan_cancel()
     {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();

        $response = $this->actingAs($admin, 'admin')->get(route('subscription.cancel'));

        $response->assertRedirect(route('admin.home'));
     }


// 有料プラン解約機能
    // 未ログインのユーザーは有料プランを解約できない
    public function test_guest_cannot_destroy_paid_plan()
    {    
        $request_parameter = ['paymentMethodId' => 'pm_card_visa'];
  
        $response = $this->delete(route('subscription.destroy'),$request_parameter);

        $response->assertRedirect(route('login'));
    }

    // ログイン済みの無料会員は有料プランを解約できない
    public function test_freeuser_cannot_destroy_paid_plan()
    {
        $user = User::factory()->create();

        $request_parameter = ['paymentMethodId' => 'pm_card_visa'];
  
        $response = $this->actingAs($user)->delete(route('subscription.destroy'),$request_parameter);

        $response->assertRedirect(route('subscription.create'));

    }
    
    // ログイン済みの有料会員は有料プランを解約できる
    public function test_paiduser_can_destroy_paid_plan()
    {
        $user = User::factory()->create();
        $user->newSubscription('premium_plan', 'price_1QTLrRJwj3ULRs8ZvoC1jlpj')->create('pm_card_visa');

        $request_parameter = ['paymentMethodId' => 'pm_card_visa'];

        $response = $this->actingAs($user)->delete(route('subscription.destroy'),$request_parameter);
        $response->assertRedirect(route('home'));

        $this->assertFalse($user->subscribed('premium_plan'));
    }

    // ログイン済みの管理者は有料プランを解約できない
    public function test_admin_cannot_destroy_paid_plan()
    {
        $admin = new Admin();
        $admin->email = 'admin@example.com';
        $admin->password = Hash::make('nagoyameshi');
        $admin->save();
  
        $request_parameter = ['paymentMethodId' => 'pm_card_visa'];
  
        $response = $this->actingAs($admin, 'admin')->delete(route('subscription.destroy'),$request_parameter);
         
        $response->assertRedirect(route('admin.home'));
    }
}