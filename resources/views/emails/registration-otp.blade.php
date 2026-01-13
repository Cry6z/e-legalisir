@component('mail::message')
# {{ __('Halo, :name!', ['name' => $name]) }}

{{ __('Gunakan kode OTP berikut untuk menyelesaikan pendaftaran akun e-Legalisir Anda:') }}

@component('mail::panel')
**{{ $otpCode }}**
@endcomponent

{{ __('Kode ini hanya berlaku selama :minutes menit. Jangan bagikan kode kepada siapa pun.', ['minutes' => 10]) }}

{{ __('Jika Anda tidak merasa melakukan pendaftaran, abaikan email ini.') }}

{{ __('Terima kasih,') }}  
{{ config('app.name') }}
@endcomponent
