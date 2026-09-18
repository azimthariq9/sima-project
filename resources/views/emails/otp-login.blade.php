<x-mail::message>
# SIMA Login OTP Code

Hello,

You are trying to sign in to SIMA using email **{{ $email }}**.

Use the following OTP code to continue:

<x-mail::panel>
# {{ $otp }}
</x-mail::panel>

This code is **valid for 10 minutes**. Do not share this code with anyone.

If you did not make this request, ignore this email.

Regards,<br>
{{ config('app.name') }}
</x-mail::message>
