@component('mail::message')
Hello {{ $user->name }},

Your two-factor code is: **{{ $code }}**

@if(!empty($expiration))
The code expires in: **{{ $expiration }}**
@endif
@endcomponent
