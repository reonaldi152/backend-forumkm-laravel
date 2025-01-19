<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Register</title>
</head>
<body>
    <p>Hi {{ $user->name }},</p>
    <p>Ini adalah OTP Register Anda: <strong>{{ $user->otp_register }}</strong></p>
</body>
</html>
