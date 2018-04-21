@component('vendor.mail.markdown.message')
# Dear user:
<br>

You are receiving this email because we received a user register request for your account.
<br>

If you did not request a user register, no further action is required.
<br>

@component('vendor.mail.html.button', ['url' => $url])
Verify Email
@endcomponent
<br>

If the button does not work, please copy below link to your brower URL bar, press enter to verify your email.
<br>

{{ $url}}

<br>

Thanks,<br>
{{ config('app.name') }}
@endcomponent
