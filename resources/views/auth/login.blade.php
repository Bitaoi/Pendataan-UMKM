<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <style>
        body {
            background-color: aliceblue;
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .card {
            padding: 2rem;
            width: 100%;
            max-width: 400px;
        }
        input {
            width: 100%;
            margin-bottom: 1rem;
        }
        button {
            width: 100%;
        }
    </style>
</head>
<body>

    <form method="POST" action="/login">
        @csrf  
        <div class="card shadow-sm">
            <h2 class="text-center mb-4">ADMIN BOLEH LOGIN :3</h2>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus class="form-control">
            @error('email')
                <div class="text-danger">{{ $message }}</div>
            @enderror

            <label for="password">Password</label>
            <input type="password" name="password" id="password" required class="form-control">

            <button type="submit" class="btn btn-primary mt-3">Login</button>
        </div>
    </form>

    <script>
        const toggle = document.getElementById('togglePassword');
        const password = docunment.getElementById('password');

        toggle.addEventListener('click', function ()) {
            const type = password.getAtribute('type') === 'password' ? 'text' : 'password';
            password.SetAtribute('type',type);

            //ganti ikon 
            this.text.Content = type === 'password' ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>'

        }

</body>
</html>
