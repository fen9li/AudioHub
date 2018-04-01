<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class BasicWebTest extends DuskTestCase
{
    public function testWebPageTitle()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSee('Laravel')
                    ->assertSee('DOCUMENTATION')
                    ->assertTitle('AudioHub');
        });
    }

}
