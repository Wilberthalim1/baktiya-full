<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PT. Baktiya Utama Indonesia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #1a237e, #283593); min-height: 100vh; display: flex; align-items: center; }
        .login-card { border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="text-center mb-4">
                <h3 class="text-white fw-bold">PT. Baktiya Utama Indonesia</h3>
                <p class="text-white-50">Sistem Manajemen Persediaan & Penjualan</p>
            </div>
            <div class="card login-card">
                <div class="card-body p-4">
                    <h5 class="text-center mb-4 fw-bold">Masuk ke Sistem</h5>
                    @if($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                    @endif
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="remember" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">Ingat saya</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Masuk</button>
                    </form>
                    <hr>
                    <small class="text-muted d-block text-center">
                        admin@baktiya.com / password
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
