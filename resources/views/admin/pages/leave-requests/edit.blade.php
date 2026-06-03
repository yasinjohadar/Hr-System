@extends('admin.layouts.master')

@section('page-title')
    تعديل طلب إجازة
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-leave-requests.css') }}">
@endpush

@section('content')
    <div class="main-content app-content admin-leave-requests-page">
        <div class="container-fluid pt-4">

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card page-hero mb-4">
                <div class="card-body py-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="page-hero-icon">
                                <i class="ri-pencil-line"></i>
                            </div>
                            <div>
                                <h4 class="mb-1 page-hero-title fw-bold">تعديل طلب إجازة</h4>
                                <p class="mb-0 page-hero-subtitle">
                                    {{ $leaveRequest->employee->full_name ?? '' }}
                                    · {{ $leaveRequest->leaveType->name_ar ?? $leaveRequest->leaveType->name }}
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('admin.leave-requests.show', $leaveRequest->id) }}" class="btn btn-hero-outline btn-sm">
                            <i class="ri-arrow-right-line me-1"></i>العودة للتفاصيل
                        </a>
                    </div>
                </div>
            </div>

            <div class="form-panel">
                <div class="form-panel-body">
                    <form method="POST" action="{{ route('admin.leave-requests.update', $leaveRequest->id) }}" id="leaveRequestForm">
                        @csrf
                        @method('PUT')

                        @include('admin.pages.leave-requests._form_fields', [
                            'leaveRequest' => $leaveRequest,
                            'employees' => $employees,
                            'leaveTypes' => $leaveTypes,
                        ])

                        <div class="form-actions">
                            <a href="{{ route('admin.leave-requests.show', $leaveRequest->id) }}" class="btn btn-form-cancel">إلغاء</a>
                            <button type="submit" class="btn btn-form-submit">
                                <i class="ri-save-line me-1"></i>حفظ التعديلات
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
@stop

@push('scripts')
    @include('admin.pages.leave-requests._form_scripts')
@endpush
