<!DOCTYPE html>
<html>
<head>
    <title>Kode OTP Pendaftaran</title>
</head>
<body>
    <p>Halo {{ $name }},</p>
    <p>Terima kasih telah mendaftar di platform kami. Berikut adalah kode OTP untuk verifikasi pendaftaran Anda:</p>
    <h2>{{ $otp }}</h2>
    <p>Gunakan kode ini untuk menyelesaikan proses pendaftaran Anda.</p>
    <p>Nama Toko: {{ $store_name }}</p>
    <p>Salam, <br> Tim Kami</p>
</body>
</html>
