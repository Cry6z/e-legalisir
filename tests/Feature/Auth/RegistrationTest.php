<?php

use App\Mail\RegistrationOtpMail;
use App\Models\RegistrationOtp;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertStatus(200);
});

test('user can request OTP during registration', function () {
    Mail::fake();

    $response = $this->post(route('register.otp.request'), [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('register.otp.show', absolute: false))
        ->assertSessionHas('status', 'otp-sent');

    $token = session('registration_otp_token');

    expect($token)->not->toBeNull();

    $otp = RegistrationOtp::where('email', 'jane@example.com')->first();

    expect($otp)->not->toBeNull();

    Mail::assertSent(RegistrationOtpMail::class, fn ($mail) => $mail->hasTo('jane@example.com'));
});

test('user can verify OTP and complete registration', function () {
    $token = Str::random(64);

    RegistrationOtp::create([
        'name' => 'Rani',
        'email' => 'rani@example.com',
        'encrypted_password' => Crypt::encryptString('password'),
        'otp_code' => '123456',
        'otp_expires_at' => now()->addMinutes(10),
        'session_token' => $token,
    ]);

    $response = $this
        ->withSession(['registration_otp_token' => $token])
        ->post(route('register.otp.verify'), ['otp' => '123456']);

    $response->assertRedirect(route('dashboard', absolute: false));

    $user = User::where('email', 'rani@example.com')->first();

    expect($user)->not->toBeNull();
    $this->assertAuthenticatedAs($user);
    $this->assertDatabaseMissing('registration_otps', ['email' => 'rani@example.com']);
});