<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: sans-serif; background:#111; color:#fff; }
        .box { max-width:400px; margin:100px auto; padding:20px; background:#1f2937; }
        input, button { width:100%; padding:10px; margin-top:10px; }
        button { background:#3b82f6; color:white; border:none; }
        .error { color:#f87171; margin-top:10px; }
    </style>
</head>
<body>

<div class="box">
    <h2>Admin Login</h2>

    @if ($errors->any())
        <div class="error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login.post') }}">
        @csrf

        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>

        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>
