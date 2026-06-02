<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $vacancy->title ?? $vacancy->job_title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <a href="{{ route('careers.index') }}" class="btn btn-link mb-3">← جميع الوظائف</a>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <h1>{{ $vacancy->title ?? $vacancy->job_title }}</h1>
    <div class="mb-4">{!! nl2br(e($vacancy->description ?? '')) !!}</div>
    <div class="card">
        <div class="card-header">تقديم طلب</div>
        <div class="card-body">
            <form method="POST" action="{{ route('careers.apply', $vacancy->id) }}" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6"><input name="first_name" class="form-control" placeholder="الاسم الأول" required value="{{ old('first_name') }}"></div>
                    <div class="col-md-6"><input name="last_name" class="form-control" placeholder="اسم العائلة" required value="{{ old('last_name') }}"></div>
                    <div class="col-md-6"><input type="email" name="email" class="form-control" placeholder="البريد" required value="{{ old('email') }}"></div>
                    <div class="col-md-6"><input name="phone" class="form-control" placeholder="الجوال" value="{{ old('phone') }}"></div>
                    <div class="col-12"><textarea name="cover_letter" class="form-control" rows="4" placeholder="رسالة تعريفية">{{ old('cover_letter') }}</textarea></div>
                    <div class="col-12"><input type="file" name="resume" class="form-control" accept=".pdf,.doc,.docx"></div>
                    <div class="col-12"><button type="submit" class="btn btn-primary">إرسال الطلب</button></div>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
