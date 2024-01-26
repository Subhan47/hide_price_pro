<!DOCTYPE html>
<html>
<head>
    <title>Welcome to Your App</title>
</head>
<body>
<h2>Welcome to Your App, {{ $user['name'] }}!</h2>
<p>We're excited to have you on board. Thank you for choosing Your App.</p>
<p>Your account has been created with the following details:</p>
<ul>
    <li>Name: {{ $user['name'] }}</li>
    <li>Email: {{ $user['email'] }}</li>
</ul>
<p>If you have any questions or need assistance, feel free to contact us.</p>
<p>Best regards,<br>Your App Team</p>
</body>
</html>
