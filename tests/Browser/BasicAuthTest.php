<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use App\User;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Foundation\Testing\WithoutMiddleware;

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

    // Dusk can't work with mocking together at the time of this testing
    public function testRegister()
    {
        $this->browse(function ($browser) {
            $browser->visit('/register')
                    ->value('#name', 'test01')
                    ->value('#email', 'test01@yahoo.com')
                    ->value('#password', 'password')
                    ->value('#password-confirm', 'password')
                    ->press('Register')
                    ->pause(1000)
                    //->assertPathIs('/home')
                    ->assertPathIs('/')
                    ->assertSee("We have send you the verification link. Please check your email ...");
        });
    }

    // Dusk can't work with mocking together at the time of this testing
     public function testLogin()
     {
         $this->createBin();

         $this->browse(function ($browser) {
             $browser->visit('/login')
                     ->value('#email', $this->user->email)
                     ->value('#password', 'password')
                     ->press('Login')
                     ->assertPathIs('/')
                     ->assertSee('We have send you the email verification link. Please verify your email first.Thank you for using this application.');
         });

         $this->deleteBin();
     }

     public function testResetPassword()
     {
        $this->createBin();
        $this->browse(function ($browser) {
             $browser->visit('/password/reset')
                     ->value('#email', $this->user->email)
                     ->press('Send Password Reset Link')
                     ->assertPathIs('/password/reset')
                     ->assertSee('We have e-mailed your password reset link!');
         });
         $this->deleteBin();
     }

     private function createBin()
     { 
         //var_dump($user->email);
         $this->user->email = 'test01@yahoo.com';
         $this->user->password = bcrypt('password');
         $this->user->save();
     }
     
     private function deleteBin()
     {
         // Tear Down
         // Delete test user
         User::where(['email' => $this->user->email])->delete();
     }
}
