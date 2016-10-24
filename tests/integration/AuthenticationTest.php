<?php

use App\User;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Lang;

class AuthenticationTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function registering_a_fresh_user()
    {
        $faker = (new Faker\Factory())->create();
        $user_email = $faker->email;
        $this->notSeeInDatabase('users',['email'=>$user_email]);
        $user = User::create(['name'=>$faker->name, 'email'=>$user_email, 'password'=> bcrypt('secret')]);
        $this->seeInDatabase('users',['id'=>$user->id,'active'=>false]);
        $this->actingAs($user);
        $this->visit('/')->seePageIs('/home');
    }

    /** @test */
    public function activating_a_signed_in_user_account()
    {
        $user = factory(App\User::class)->create();
        $this->seeInDatabase('users',['id'=>$user->id,'active'=>false]);
        $this->actingAs($user);
        $this->visit('/home');
        $this->visit('/activation/'.$user->activation_token)->seePageIs('/home');
           // ->see(Lang::get('messages.activated_message',['email'=>$user->email]));
    }

    /** @test */
    public function activating_a_signed_out_user_account()
    {
        $user = factory(App\User::class)->create();
        $this->seeInDatabase('users',['id'=>$user->id,'active'=>false]);
        $this->actingAs($user);
        $this->visit('/home');
        $this->app->make('auth')->guard()->logout();
        $this->visit('/activation/'.$user->activation_token)
            ->seePageIs('/')
            ->see(Lang::get('messages.activated_message2',['email'=>$user->email]));
    }

    /** @test */
    public function redirecting_non_authenticated()
    {
        $this->visit('/home')
            ->seePageIs('/login');
    }


}
