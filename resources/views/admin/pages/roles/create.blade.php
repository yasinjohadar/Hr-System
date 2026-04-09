@extends('admin.layouts.master')

@section('page-title')
    إنشاء دور جديد
@stop

@section('css')
@stop

@section('content')
    @if (\Session::has('success'))
        <div class="alert alert-success">
            <ul>
                <li>{!! \Session::get('success') !!}</li>
            </ul>
        </div>
    @endif

    @if (\Session::has('error'))
        <div class="alert alert-danger">
            <ul>
                <li>{!! \Session::get('error') !!}</li>
            </ul>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Start::app-content -->
    <div class="main-content app-content">
        <div class="container-fluid">

            <!-- Page Header -->
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">إنشاء دور جديد</h5>
                </div>
            </div>
            <!-- Page Header Close -->

            <!-- Start::row -->
            <div class="row">
                <div class="col-xl-12">
                    <div class="card shadow-sm border-0 ">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 fw-bold">بيانات الدور</h6>
                        </div>
                        <div class="card-body">

                            <form id="role-create-form" method="POST" action="{{ route('roles.store') }}">
                                @csrf

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">اسم الدور</label>
                                        <input type="text" class="form-control" name="name" placeholder="مثال: مشرف عام">
                                    </div>
                                </div>

                                <div class="sticky-top py-2 mb-3 border-bottom d-flex justify-content-end gap-2 align-items-center bg-body"
                                    style="z-index: 1020;">
                                    <a href="{{ route('roles.index') }}" class="btn btn-secondary">إلغاء</a>
                                    <button type="submit" class="btn btn-primary">حفظ الدور</button>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold d-block mb-3">الصلاحيات</label>
                                    @foreach ($permissionsGrouped as $categoryName => $permissions)
                                        <div class="card card-bordered mb-3">
                                            <div class="card-header bg-light py-2">
                                                <h6 class="mb-0 fw-bold">{{ $categoryName }}</h6>
                                            </div>
                                            <div class="card-body py-2">
                                                <div class="row">
                                                    @foreach ($permissions->split(3) as $chunk)
                                                        <div class="col-md-4">
                                                            <ul class="list-unstyled mb-0">
                                                                @foreach ($chunk as $permission)
                                                                    <li class="mb-1">
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                name="permissions[{{ $permission->name }}]"
                                                                                value="{{ $permission->name }}"
                                                                                id="perm_create_{{ $permission->id }}">
                                                                            <label class="form-check-label small" for="perm_create_{{ $permission->id }}"
                                                                                title="{{ $permission->name }}">
                                                                                {{ permission_label($permission->name) }}
                                                                            </label>
                                                                        </div>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                                    <a href="{{ route('roles.index') }}" class="btn btn-secondary">إلغاء</a>
                                    <button type="submit" class="btn btn-primary">حفظ الدور</button>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>
            </div>
            <!--End::row-->

        </div>
    </div>
    <!-- End::app-content -->
@stop
