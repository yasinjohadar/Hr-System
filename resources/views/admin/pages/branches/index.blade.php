@extends('admin.layouts.master')

@section('page-title')
    قائمة الفروع
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
                    <h5 class="page-title fs-21 mb-1">كافة الفروع</h5>
                </div>
            </div>
            <!-- Page Header Close -->

            <!-- Start::row-1 -->
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex gap-3">
                            @can('branch-create')
                            <a href="{{ route('admin.branches.create') }}" class="btn btn-primary btn-sm">إضافة فرع جديد</a>
                            @endcan

                            <div class="flex-shrink-0">
                                <div class="form-check form-switch form-switch-right form-switch-md">
                                    <form action="{{ route('admin.branches.index') }}" method="GET"
                                        class="d-flex align-items-center gap-2">
                                        {{-- حقل البحث --}}
                                        <input style="width: 300px" type="text" name="query" class="form-control"
                                            placeholder="بحث بالاسم أو الكود أو المدينة" value="{{ request('query') }}">

                                        {{-- فلتر الحالة النشطة --}}
                                        <select name="is_active" class="form-select">
                                            <option value="">كل الحالات</option>
                                            <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>نشط</option>
                                            <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>غير نشط</option>
                                        </select>

                                        {{-- فلتر الفرع الرئيسي --}}
                                        <select name="is_main" class="form-select">
                                            <option value="">كل الفروع</option>
                                            <option value="1" {{ request('is_main') == '1' ? 'selected' : '' }}>الفرع الرئيسي</option>
                                            <option value="0" {{ request('is_main') == '0' ? 'selected' : '' }}>فروع فرعية</option>
                                        </select>

                                        <button type="submit" class="btn btn-secondary">بحث</button>
                                        <a href="{{ route('admin.branches.index') }}" class="btn btn-danger">مسح</a>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle table-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col" style="width: 40px;">#</th>
                                            <th scope="col" style="min-width: 200px;">اسم الفرع</th>
                                            <th scope="col" style="min-width: 120px;">الكود</th>
                                            <th scope="col" style="min-width: 150px;">المدينة</th>
                                            <th scope="col" style="min-width: 150px;">المدير</th>
                                            <th scope="col" style="min-width: 120px;">الهاتف</th>
                                            <th scope="col" style="min-width: 100px;">عدد الموظفين</th>
                                            <th scope="col" style="min-width: 110px;">الحالة</th>
                                            <th scope="col" style="min-width: 200px;">العمليات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($branches as $branch)
                                            <tr>
                                                <th scope="row">{{ $loop->iteration }}</th>
                                                <td>
                                                    <strong>{{ $branch->name }}</strong>
                                                    @if ($branch->is_main)
                                                        <span class="badge bg-warning text-dark ms-1">رئيسي</span>
                                                    @endif
                                                    @if ($branch->description)
                                                        <br><small class="text-muted">{{ Str::limit($branch->description, 50) }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($branch->code)
                                                        <span class="badge bg-info">{{ $branch->code }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($branch->city)
                                                        <span class="text-primary">{{ $branch->city }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($branch->manager)
                                                        <span class="text-primary">{{ $branch->manager->name }}</span>
                                                    @elseif ($branch->manager_name)
                                                        <span class="text-muted">{{ $branch->manager_name }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($branch->phone)
                                                        <a href="tel:{{ $branch->phone }}" class="text-decoration-none">{{ $branch->phone }}</a>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary">{{ $branch->employees_count }}</span>
                                                </td>
                                                <td>
                                                    @if ($branch->is_active)
                                                        <span class="badge bg-success">نشط</span>
                                                    @else
                                                        <span class="badge bg-danger">غير نشط</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @can('branch-edit')
                                                    <a class="btn btn-info btn-sm me-1"
                                                        href="{{ route('admin.branches.edit', $branch->id) }}"
                                                        title="تعديل الفرع">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </a>
                                                    @endcan
                                                    @can('branch-show')
                                                    <a class="btn btn-success btn-sm me-1"
                                                        href="{{ route('admin.branches.show', $branch->id) }}"
                                                        title="عرض التفاصيل">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                    @endcan
                                                    @can('branch-delete')
                                                    <a class="btn btn-danger btn-sm me-1" data-bs-toggle="modal"
                                                        data-bs-target="#delete{{ $branch->id }}"
                                                        title="حذف الفرع">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </a>
                                                    @endcan
                                                </td>
                                            </tr>

                                            @include('admin.pages.branches.delete')
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center text-danger fw-bold">لا توجد
                                                    بيانات متاحة
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <div class="mt-3">
                                    {{ $branches->withQueryString()->links() }}
                                </div>
                            </div>
                        </div><!-- end card-body -->
                    </div><!-- end card -->
                </div>
            </div>
            <!--End::row-1 -->

        </div>
    </div>
    <!-- End::app-content -->
@stop

@section('js')
@stop

