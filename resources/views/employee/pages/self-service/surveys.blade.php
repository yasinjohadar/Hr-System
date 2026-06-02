@extends('employee.layouts.master')
@section('page-title', 'الاستبيانات')
@section('content')
<div class="container-fluid">
    <h5 class="my-4">استبيانات متاحة</h5>
    @forelse($surveys as $survey)
        <div class="card mb-3">
            <div class="card-body">
                <h6>{{ $survey->title_ar ?? $survey->title }}</h6>
                <form method="POST" action="{{ route('employee.surveys.submit', $survey->id) }}">
                    @csrf
                    @foreach($survey->questions as $q)
                        <div class="mb-2">
                            <label class="form-label small">{{ $q->question_text }}</label>
                            <input type="text" name="answers[{{ $q->id }}]" class="form-control form-control-sm" required>
                        </div>
                    @endforeach
                    <button class="btn btn-primary btn-sm">إرسال</button>
                </form>
            </div>
        </div>
    @empty
        <p class="text-muted">لا توجد استبيانات حالياً.</p>
    @endforelse
</div>
@endsection
