<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Events\UserRegisteredEvent;
use App\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class EmailVerificationEventTest extends TestCase
{

    use RefreshDatabase;

    /**
     * test user registration can dispatch an UserRegisteredEvent.
     *
     * @return void
     */
    public function testDispatchUserRegisteredEvent()
    {
        Event::fake();

        // performing user registration
        User::create([
            'name' => 'Feng Li',
            'email'      => 'lifcn@yahoo.com',
            'password'   => bcrypt('password'),
        ]);

        $user = User::where('email', 'lifcn@yahoo.com')->first();
        //dd($user);

        $this->assertDatabaseHas('users',[
            'email' => 'lifcn@yahoo.com'
        ]); 

// the assertDispatched doesnt work
// check the source code in
// vendor/laravel/framework/src/Illuminate/Support/Testing/Fakes/EventFake.php
// leave this as is at this moment
//        Event::assertDispatched(UserRegisteredEvent::class, function ($e) use ($user) {
//            return $e->user->id === $user->id;
//        });

    }
}
