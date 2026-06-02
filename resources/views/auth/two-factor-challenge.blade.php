<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المصادقة الثنائية</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-3">المصادقة الثنائية</h5>
                    <form method="POST" action="{{ route('two-factor.verify') }}">
                        @csrf
                        <div class="mb-3">
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                                placeholder="رمز التحقق" required autofocus>
                            @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn btn-primary w-100">تحقق</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
