@component('mail::message')
# Mã OTP của bạn

@component('mail::panel')
**{{ $otp }}**
@endcomponent

Mã OTP có hiệu lực trong **{{ $expiresIn }} phút**.  
**Không chia sẻ mã này với bất kỳ ai.**

Nếu bạn không yêu cầu mã này, hãy bỏ qua email.

Trân trọng,<br>
NevoPay Team
@endcomponent
