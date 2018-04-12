<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\User;
use App\Notifications\EmailVerificationNotification;
use App\Events\UserRegisteredEvent;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp() {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    /**
     * Test verify email
     *
     * @return void
     */
    public function testVerifyEmail()
    {
       Notification::fake();

       $user = $this->user;

       event(new UserRegisteredEvent($user));

       $token = DB::table('email_verifications')
                 ->where('email', $user->email)
                 ->first(['token'])->token;
       $url = url('/verify/' . $token . '?email=' . urlencode($user->email)); 
       //dd($url);
       $this->get($url)
            ->assertStatus(302);
    }
}
