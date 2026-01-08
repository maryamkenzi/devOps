<!DOCTYPE html>
<html>
<head>
    <title>Index Custom</title>
</head>
<body>

<h1>Welcome to Akaunting – Custom Index</h1>

@if (session('success'))
    <p style="color:green">{{ session('success') }}</p>
@endif

<a href="/register-custom">Go to Register</a>

</body>
</html>