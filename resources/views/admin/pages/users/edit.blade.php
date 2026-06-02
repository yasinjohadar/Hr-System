@extends('admin.layouts.master')

@section('page-title')
    تعديل المستخدم
@stop

@section('content')
    <div class="main-content app-content admin-users">
        <div class="container-fluid">
            @include('admin.pages.users.partials.alerts')

            @include('admin.pages.users.partials.page-hero', [
                'heroTitle' => 'تعديل المستخدم',
                'heroSubtitle' => $user->name,
                'heroActions' => '<a href="' . route('users.show', $user->id) . '" class="btn btn-outline-light btn-sm me-1"><i class="ri-eye-line me-1"></i>عرض</a><a href="' . route('users.index') . '" class="btn btn-outline-light btn-sm"><i class="ri-arrow-right-line me-1"></i>القائمة</a>',
            ])

            <div class="card custom-card">
                <div class="card-body">
                    <form method="POST" action="{{ route('users.update', $user->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        @include('admin.pages.users.partials.form', ['isEdit' => true, 'user' => $user, 'roles' => $roles])

                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">إلغاء</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line me-1"></i>حفظ التعديلات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-users.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/admin-users.js') }}"></script>
@endpush
