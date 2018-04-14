<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use App\User;
use Carbon\Carbon;

class CheckEmailVerificationMiddlewareTest extends DuskTestCase
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

    public function testVerifyEmail()
    {
         //var_dump($user->email);
         $this->user->email = 'lifcn@yahoo.com';
         $this->user->password = bcrypt('test01pass');
         $this->user->is_verified = 1;
         $this->user->verified_at = Carbon::yesterday();
         $this->user->save();

         $this->browse(function ($browser) {
             $browser->visit('/login')
                     ->value('#email', $this->user->email)
                     ->value('#password', 'test01pass')
                     ->press('Login')
                     ->assertPathIs('/home')
                     ->assertSee($this->user->name)
                     ->assertSee('You are logged in!');
         });

         // Tear Down
         // Delete test user
         User::where(['email' => $this->user->email])->delete();
    }
}
