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
use Illuminate\Support\Facades\Session;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $token;

    protected function setUp() {
        parent::setUp();

        Notification::fake();

        $this->user = factory(User::class)->create();
        event(new UserRegisteredEvent($this->user));

        $this->token = DB::table('email_verifications')
                 ->where('email', $this->user->email)
                 ->first(['token'])->token;
    }

    /**
     * Test verify email
     *
     * @return void
     */
    public function testVerifyEmail()
    {
       $url = url('/verify/' . $this->token . '?email=' . urlencode($this->user->email)); 
       $this->get($url)
            ->assertRedirect(route('home'))
            ->assertStatus(302)
            ->assertSessionHas(['success' => 'Your email has been verified successfully. Thank you for using this application!']);
       //dd(session()->all());
       $this->assertDatabaseHas('users',['email' => $this->user->email,'is_verified' => 1])
            ->assertDatabaseMissing('email_verifications',['email' => $this->user->email]);
    }

    /**
     * Test UserNotFoundException
     *
     * @return void
     */
    public function testUserNotFoundException()
    {
       $broken_email = substr($this->user->email,0,-1);
       $broken_email_url = url('/verify/' . $this->token . '?email=' . urlencode($broken_email));
       // dd($url);
       // "http://laradev:8000/verify/c0cf13eb4679ebb4bf34505fafcbbf9b9fd66a17dfc015c717c9171be70900ad?email=therese.mertz%40example.net"
       $this->get($broken_email_url)
            ->assertRedirect(route('register'))
            ->assertStatus(302)
            ->assertSessionHas(['message' => 'No user found for the given request. Please register first. Thank you for using this application.']);
       $this->assertDatabaseHas('users',['email' => $this->user->email,'is_verified' => 0])
            ->assertDatabaseMissing('users',['email' => $broken_email])
            ->assertDatabaseHas('email_verifications',['email' => $this->user->email]);
    }

    /**
     * Test EmailVerificationLinkBrokenException
     *
     * @return void
     */
    public function testEmailVerificationLinkBrokenException()
    {
       $broken_token = substr($this->token,0,-1);
       $broken_token_url = url('/verify/' . $broken_token . '?email=' . urlencode($this->user->email));
       // dd($url);
       // "http://laradev:8000/verify/c0cf13eb4679ebb4bf34505fafcbbf9b9fd66a17dfc015c717c9171be70900ad?email=therese.mertz%40example.net"
       $this->get($broken_token_url)
            ->assertRedirect(route('login'))
            ->assertStatus(302)
            ->assertSessionHas(['message' => 'The email verification link is broken. Enter your registered email, and press Login, you will receive another email verification link. Thank you for using this application.']);
       $this->assertDatabaseHas('users',['email' => $this->user->email,'is_verified' => 0])
            ->assertDatabaseMissing('email_verifications',['email' => $this->user->email, 'token' => $broken_token]);
    }

}
