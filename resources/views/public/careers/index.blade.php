<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الوظائف الشاغرة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <h1 class="mb-4">الوظائف الشاغرة</h1>
    @forelse($vacancies as $vacancy)
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">{{ $vacancy->title ?? $vacancy->job_title }}</h5>
                <p class="text-muted small">{{ $vacancy->department?->name }} — {{ $vacancy->position?->name }}</p>
                <a href="{{ route('careers.show', $vacancy->id) }}" class="btn btn-primary btn-sm">التفاصيل والتقديم</a>
            </div>
        </div>
    @empty
        <p class="text-muted">لا توجد وظائف منشورة حالياً.</p>
    @endforelse
    {{ $vacancies->links() }}
</div>
</body>
</html>
