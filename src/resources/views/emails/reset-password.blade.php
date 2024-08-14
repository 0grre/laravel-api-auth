<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reset Password</title>
</head>
<body>
<p>Hello,</p>
<p>You have requested a password reset. Click the link below to reset your password:</p>
<p>
    <a href="{{ url('reset-password?token=' . $token) }}">
        Reset my password
    </a>
</p>
<p>If you did not request this reset, ignore this email.</p>
</body>
</html>
