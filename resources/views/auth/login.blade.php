<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Ayam Goreng Cipanas Indah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --char: #241c1a;
            --ember: #d64933;
            --ember-dark: #b83a27;
            --golden: #e8a33d;
            --cream: #faf6f1;
            --ink: #2a211e;
        }
        body {
            background-color: var(--char);
            background-image:
                radial-gradient(circle at 15% 20%, rgba(232,163,61,0.10) 0, transparent 40%),
                radial-gradient(circle at 85% 80%, rgba(214,73,51,0.14) 0, transparent 45%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0,0,0,0.35);
            background: #fff;
        }
        .login-header {
            background-color: var(--char);
            color: #fff;
            text-align: center;
            padding: 32px 24px 22px;
            position: relative;
        }
        .login-header::after {
            content: "";
            position: absolute;
            bottom: -5px;
            left: 0;
            right: 0;
            height: 10px;
            background-image: radial-gradient(circle at 10px 0, #fff 5px, transparent 5.5px);
            background-size: 20px 10px;
            background-repeat: repeat-x;
        }
        .login-header h4 {
            font-family: 'Fraunces', serif;
            font-weight: 700;
            margin: 0;
        }
        .login-header small {
            color: var(--golden);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 11px;
            font-weight: 600;
        }
        .login-body { padding: 32px 28px 28px; }
        .form-label { font-weight: 500; font-size: 13.5px; color: var(--ink); }
        .form-control {
            border-radius: 9px;
            border-color: #e8ddd3;
            padding: 10px 12px;
        }
        .form-control:focus {
            border-color: var(--ember);
            box-shadow: 0 0 0 0.2rem rgba(214,73,51,0.15);
        }
        .btn-login {
            background-color: var(--ember);
            border-color: var(--ember);
            border-radius: 9px;
            font-weight: 600;
            padding: 10px;
        }
        .btn-login:hover { background-color: var(--ember-dark); border-color: var(--ember-dark); color: #fff; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <h4>Cipanas Indah</h4>
            <small>Sistem Manajemen Stok &amp; Transaksi</small>
        </div>
        <div class="login-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input
                        type="text"
                        name="username"
                        class="form-control"
                        value="{{ old('username') }}"
                        required
                        autofocus
                    >
                </div>
                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        required
                    >
                </div>
                <button type="submit" class="btn btn-login text-white w-100">
                    Masuk
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
