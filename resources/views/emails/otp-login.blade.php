<x-mail::message>
# Kode OTP Login SIMA

Halo,

Anda mencoba masuk ke SIMA menggunakan email **{{ $email }}**.

Gunakan kode OTP berikut untuk melanjutkan:

<x-mail::panel>
# {{ $otp }}
</x-mail::panel>

Kode ini **berlaku selama 10 menit**. Jangan bagikan kode ini kepada siapapun.

Jika Anda tidak melakukan permintaan ini, abaikan email ini.

Salam,<br>
{{ config('app.name') }}
</x-mail::message>
