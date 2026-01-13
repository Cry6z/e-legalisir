<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\RegistrationOtpMail;
use App\Models\RegistrationOtp;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegistrationOtpController extends Controller
{
    private const OTP_TTL_MINUTES = 10;
    private const MAX_ATTEMPTS = 5;

    public function requestOtp(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class, 'email'),
            ],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        RegistrationOtp::where('email', $validated['email'])->delete();

        $otpCode = (string) random_int(100000, 999999);
        $sessionToken = Str::random(64);

        $otp = RegistrationOtp::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'encrypted_password' => Crypt::encryptString($validated['password']),
            'otp_code' => $otpCode,
            'otp_expires_at' => now()->addMinutes(self::OTP_TTL_MINUTES),
            'session_token' => $sessionToken,
        ]);

        Mail::to($otp->email)->send(new RegistrationOtpMail($otp->name, $otpCode));

        $request->session()->put('registration_otp_token', $sessionToken);

        return redirect()
            ->route('register.otp.show')
            ->with('status', 'otp-sent');
    }

    public function showOtpForm(Request $request): RedirectResponse|View
    {
        $token = $request->session()->get('registration_otp_token');

        if (! $token) {
            return redirect()->route('register');
        }

        $otp = RegistrationOtp::where('session_token', $token)->first();

        if (! $otp) {
            $request->session()->forget('registration_otp_token');

            return redirect()->route('register');
        }

        return view('livewire.auth.register-verify-otp', [
            'email' => $otp->email,
        ]);
    }

    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $token = $request->session()->get('registration_otp_token');

        if (! $token) {
            return redirect()->route('register')->withErrors([
                'otp' => __('Sesi OTP tidak ditemukan. Silakan daftar kembali.'),
            ]);
        }

        $otp = RegistrationOtp::where('session_token', $token)->first();

        if (! $otp) {
            $request->session()->forget('registration_otp_token');

            return redirect()->route('register')->withErrors([
                'otp' => __('Kode OTP tidak ditemukan. Silakan daftar kembali.'),
            ]);
        }

        if ($otp->otp_expires_at->isPast()) {
            $otp->delete();
            $request->session()->forget('registration_otp_token');

            return redirect()->route('register')->withErrors([
                'otp' => __('Kode OTP sudah kedaluwarsa. Silakan daftar kembali.'),
            ]);
        }

        if ($otp->attempts >= self::MAX_ATTEMPTS) {
            $otp->delete();
            $request->session()->forget('registration_otp_token');

            return redirect()->route('register')->withErrors([
                'otp' => __('Terlalu banyak percobaan OTP. Silakan daftar kembali.'),
            ]);
        }

        if ($otp->otp_code !== $request->input('otp')) {
            $otp->increment('attempts');

            return back()->withErrors([
                'otp' => __('Kode OTP tidak sesuai.'),
            ]);
        }

        $user = User::create([
            'name' => $otp->name,
            'email' => $otp->email,
            'password' => Crypt::decryptString($otp->encrypted_password),
            'role' => 'alumni',
        ]);

        $user->forceFill([
            'email_verified_at' => now(),
        ])->save();

        $otp->delete();
        $request->session()->forget('registration_otp_token');

        Auth::login($user);

        return redirect()->intended(route('dashboard'));
    }

    public function resendOtp(Request $request): RedirectResponse
    {
        $token = $request->session()->get('registration_otp_token');

        if (! $token) {
            return redirect()->route('register');
        }

        $otp = RegistrationOtp::where('session_token', $token)->first();

        if (! $otp) {
            $request->session()->forget('registration_otp_token');

            return redirect()->route('register');
        }

        $otpCode = (string) random_int(100000, 999999);
        $otp->update([
            'otp_code' => $otpCode,
            'otp_expires_at' => now()->addMinutes(self::OTP_TTL_MINUTES),
            'attempts' => 0,
        ]);

        Mail::to($otp->email)->send(new RegistrationOtpMail($otp->name, $otpCode));

        return back()->with('status', 'otp-resent');
    }
}
