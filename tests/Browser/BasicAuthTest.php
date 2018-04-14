<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use App\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Auth\Notifications\ResetPassword;
use App\Notifications\EmailVerificationNotification;

class BasicAuthTest extends DuskTestCase
{
    use DatabaseMigrations;

    private $user;

    protected function setUp() {
        parent::setUp(); // this is important!

        $this->user = factory(User::class)->create();
    }

    protected function tearDown() {
        parent::tearDown(); // this is important!

        // Clear cookies
        foreach (static::$browsers as $browser) {
          $browser->driver->manage()->deleteAllCookies();
        }
    } 

    public function testRegisterLink()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSee('Register')
                    ->clickLink('Register')
                    //->pause(1000)
                    ->assertSeeIn('form','Register');
        });
    }
    public function testLoginLink()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSee('Login')
                    ->clickLink('Login')
                    //->pause(1000)
                    ->assertSeeIn('form','Login');
        });
    }

   /*
    // stop this test to avoid sending emails
    // mock can't work with mocking together at this moment

    public function testRegister()
    {
        $this->browse(function ($browser) {
            $browser->visit('/register')
                    ->value('#name', 'Feng Li')
                    ->value('#email', 'lifcn@yahoo.com')
                    ->value('#password', 'password')
                    ->value('#password-confirm', 'password')
                    ->press('Register')
                    ->pause(1000)
                    //->assertPathIs('/home')
                    ->assertPathIs('/')
                    ->assertSee("We have send you the verification link. Please check your email ...");
        });
    }
    */


   /*
    // stop this test to avoid sending emails
    // mock can't work with mocking together at this moment
     // test login user failed to verify his email

     public function testLogin()
     {
         //var_dump($user->email);
         $this->user->email = 'lifcn@yahoo.com';
         $this->user->password = bcrypt('test01pass');  
         $this->user->save();

         $this->browse(function ($browser) {
             $browser->visit('/login')
                     ->value('#email', $this->user->email)
                     ->value('#password', 'test01pass')
                     ->press('Login')
                     ->assertPathIs('/')
                     ->assertSee('We have send you the email verification link. Please verify your email first.Thank you for using this application.');
         });

         // Tear Down
         // Delete test user
         User::where(['email' => $this->user->email])->delete();
     }

     */

     public function testResetPassword()
     {
        Notification::fake();

        $this->browse(function ($browser) {
             $browser->visit('/password/reset')
                     ->value('#email', $this->user->email)
                     ->press('Send Password Reset Link')
                     ->assertPathIs('/password/reset')
                     ->assertSee('We have e-mailed your password reset link!');
         });

        Notification::hasSent($this->user, ResetPassword::class);
     }

}
