@extends('admin.layouts.master')

@section('page-title')
    إضافة طلب إجازة جديد
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
                                <i class="ri-add-line"></i>
                            </div>
                            <div>
                                <h4 class="mb-1 page-hero-title fw-bold">إضافة طلب إجازة</h4>
                                <p class="mb-0 page-hero-subtitle">تسجيل طلب إجازة جديد لموظف</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.leave-requests.index') }}" class="btn btn-hero-outline btn-sm">
                            <i class="ri-arrow-right-line me-1"></i>العودة للقائمة
                        </a>
                    </div>
                </div>
            </div>

            <div class="form-panel">
                <div class="form-panel-body">
                    <form method="POST" action="{{ route('admin.leave-requests.store') }}" id="leaveRequestForm">
                        @csrf

                        @include('admin.pages.leave-requests._form_fields', [
                            'employees' => $employees,
                            'leaveTypes' => $leaveTypes,
                        ])

                        <div class="form-actions">
                            <a href="{{ route('admin.leave-requests.index') }}" class="btn btn-form-cancel">إلغاء</a>
                            <button type="submit" class="btn btn-form-submit">
                                <i class="ri-save-line me-1"></i>حفظ طلب الإجازة
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
