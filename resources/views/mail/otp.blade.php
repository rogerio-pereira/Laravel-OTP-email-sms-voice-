<x-mail::message>
Hello {{ $otp->user->name }} your new One time password is:

# {{ $otp->otp }}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
