<x-mail::message>
# Account Verification

Welcome to {{config('app.name')}}. Please verify your account verification using below link.
Verify your account by clicking [here]({{$url}}) 

<x-mail::button :url="''">
Button Text
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
