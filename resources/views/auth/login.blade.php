<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    </head>
<body>

    <h2>Silakan Login</h2>

    <form method="POST" action="{{ route('login') }}">
        @csrf  <div>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus>
            @error('email')
                <div style="color: red;">{{ $message }}</div>
            @enderror
        </div>

        <br>

        <div>
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
        </div>

        <br>

        <div>
            <button type="submit">Login</button>
        </div>
    </form>

</body>
</html>