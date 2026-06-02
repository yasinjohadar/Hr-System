@extends('employee.layouts.master')
@section('page-title', 'مهام الاستقبال')
@section('content')
<div class="container-fluid">
    <h5 class="my-4">مهام الاستقبال</h5>
    @if(!$process)
        <p class="text-muted">لا توجد عملية استقبال نشطة.</p>
    @else
        <div class="progress mb-3" style="height:8px;"><div class="progress-bar" style="width:{{ $process->completion_percentage }}%"></div></div>
        @foreach($process->checklists as $item)
            <div class="card mb-2">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ $item->task?->title_ar ?? $item->task?->title }}</strong>
                        <span class="badge bg-secondary ms-2">{{ $item->status }}</span>
                    </div>
                    @if($item->status !== 'completed')
                        <form method="POST" action="{{ route('employee.onboarding.complete-task', $item->id) }}">@csrf
                            <button class="btn btn-sm btn-success">تم الإنجاز</button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection
