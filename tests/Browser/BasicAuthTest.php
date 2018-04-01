<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class BasicAuthTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function testRegisterLink()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSee('REGISTER')
                    ->clickLink('Register')
                    //->pause(1000)
                    ->assertSeeIn('form','Register');
        });
    }
    public function testLoginLink()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSee('LOGIN')
                    ->clickLink('Login')
                    //->pause(1000)
                    ->assertSeeIn('form','Login');
        });
    }

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
                    ->assertPathIs('/home')
                    ->assertSee("You are logged in!");
        });
    }

}
