<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Events\UserRegisteredEvent;
use App\Listeners\SaveEmailVerificationToken;
use App\Notifications\EmailVerificationNotification;
use App\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class EmailVerificationEventTest extends TestCase
{

    use RefreshDatabase;

    private $user;

    protected function setUp() {
        parent::setUp(); 

        $this->user = factory(User::class)->create();
    }

    /**
     * test user registration can dispatch an UserRegisteredEvent.
     * @return void
     */
    public function testUserRegisteredEventDispatched()
    {
        Event::fake();  // dont trigger listeners

        $user = $this->user;
        event(new UserRegisteredEvent($user));

        // assert event is dispatched againest right user
        Event::assertDispatched(UserRegisteredEvent::class, function ($e) use ($user) {
            return $e->user->id === $user->id;
        });
    
        // assert event is dispatch 1 time only
        Event::assertDispatched(UserRegisteredEvent::class, 1);
    }

    /**
     * test UserRegisteredEvent can trigger 'SaveEmailVerificationToken' Listener
     * test UserRegisteredEvent can trigger 'SendEmailVerificationNotification' Listener
     * @return void
     */
    public function testListeners()
    {
        Notification::fake();

        $user = $this->user;
        //dd($user);
        // fire UserRegisteredEvent
        event(new UserRegisteredEvent($user));

        $this->assertDatabaseHas('email_verifications',['email' => $user->email]);       

        Notification::assertSentTo(
            [$user], EmailVerificationNotification::class
        );
    }

}
