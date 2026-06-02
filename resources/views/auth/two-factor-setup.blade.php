@extends('layouts.app')
@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-body">
            <h5>تفعيل المصادقة الثنائية</h5>
            <p class="small text-muted">امسح الرمز أو أدخل المفتاح يدوياً: <code>{{ $secret }}</code></p>
            <p class="small">رابط QR: <a href="{{ $qrUrl }}" target="_blank">{{ $qrUrl }}</a></p>
            <form method="POST" action="{{ route('two-factor.confirm') }}" class="mt-3">
                @csrf
                <div class="mb-3">
                    <label class="form-label">رمز التحقق</label>
                    <input type="text" name="code" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">تأكيد التفعيل</button>
            </form>
        </div>
    </div>
</div>
@endsection
