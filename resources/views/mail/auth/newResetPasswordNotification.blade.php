@component('vendor.mail.markdown.message')
# Dear user:
<br>

You are receiving this email because we received a password reset request for your account.
<br>

If you did not request a password reset, no further action is required.
<br>

@component('vendor.mail.html.button', ['url' => $url])
Reset Password
@endcomponent
<br>

If the button does not work, please copy below link to your brower URL bar, press enter to reset your password.
<br>

{{ $url}}

<br>

Thanks,<br>
{{ config('app.name') }}
@endcomponent
