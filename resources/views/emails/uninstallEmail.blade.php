<!DOCTYPE html>
<html>
<head>
    <title>Uninstallation Feedback</title>
</head>
<body>
<h2>We're Sorry to See You Go, {{ $user['name'] }}!</h2>
<p>We regret to inform you that your App has been uninstalled.</p>
<p>Your account details:</p>
<ul>
    <li>Name: {{ $user['name'] }}</li>
    <li>Email: {{ $user['email'] }}</li>
</ul>
<p>If you uninstalled by mistake or have any feedback, please let us know. We're here to help!</p>
<p>Thank you for trying. If you ever decide to come back, we'll be happy to welcome you again.</p>
<p>Best regards</p>
</body>
</html>
