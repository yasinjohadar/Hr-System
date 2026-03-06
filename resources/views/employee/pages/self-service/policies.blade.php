@extends('employee.layouts.master')

@section('page-title')
    السياسات واللوائح
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">السياسات واللوائح</h5>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('info'))
                <div class="alert alert-info">{{ session('info') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <!-- سياسات مطلوب الاعتراف بها -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">سياسات مطلوب الاعتراف بها ({{ $policiesPending->total() }})</h5>
                </div>
                <div class="card-body">
                    @forelse($policiesPending as $policy)
                        <div class="border rounded p-3 mb-3">
                            <h6 class="mb-2">{{ $policy->title }}</h6>
                            @if($policy->category)
                                <span class="badge bg-secondary mb-2">{{ $policy->category }}</span>
                            @endif
                            @if($policy->effective_date)
                                <p class="text-muted small mb-2">تاريخ السريان: {{ $policy->effective_date->format('Y-m-d') }}</p>
                            @endif
                            <div class="mb-3 text-muted small">
                                {!! \Illuminate\Support\Str::limit(strip_tags($policy->content), 300) !!}
                            </div>
                            <form action="{{ route('employee.policies.acknowledge') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="policy_id" value="{{ $policy->id }}">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-check me-1"></i>أقر بأنني اطلعت
                                </button>
                            </form>
                            @if($policy->document_path)
                                <a href="{{ asset('storage/' . $policy->document_path) }}" target="_blank" class="btn btn-outline-secondary btn-sm ms-2">
                                    <i class="fas fa-download me-1"></i>تحميل المستند
                                </a>
                            @endif
                        </div>
                    @empty
                        <p class="text-muted mb-0">لا توجد سياسات مطلوب الاعتراف بها حالياً.</p>
                    @endforelse
                    @if($policiesPending->hasPages())
                        <div class="mt-3">{{ $policiesPending->links() }}</div>
                    @endif
                </div>
            </div>

            <!-- سياسات تم الاعتراف بها -->
            @if($policiesAcknowledged->isNotEmpty())
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">سياسات تم الاعتراف بها</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach($policiesAcknowledged as $policy)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>{{ $policy->title }}</span>
                                <span class="badge bg-success">تم الاعتراف في {{ $policy->acknowledgments->first()?->acknowledged_at?->format('Y-m-d') }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
        </div>
    </div>
@stop
