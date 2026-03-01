@extends('admin.layouts.master')

@section('page-title')
    قائمة المناصب
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

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">كافة المناصب</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex gap-3">
                            @can('position-create')
                            <a href="{{ route('admin.positions.create') }}" class="btn btn-primary btn-sm">إضافة منصب جديد</a>
                            @endcan

                            <div class="flex-shrink-0">
                                <form action="{{ route('admin.positions.index') }}" method="GET" class="d-flex align-items-center gap-2">
                                    <input style="width: 300px" type="text" name="query" class="form-control"
                                        placeholder="بحث بالاسم أو الكود" value="{{ request('query') }}">
                                    <select name="department_id" class="form-select">
                                        <option value="">كل الأقسام</option>
                                        @foreach ($departments as $dept)
                                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                                {{ $dept->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <select name="is_active" class="form-select">
                                        <option value="">كل الحالات</option>
                                        <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>نشط</option>
                                        <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>غير نشط</option>
                                    </select>
                                    <button type="submit" class="btn btn-secondary">بحث</button>
                                    <a href="{{ route('admin.positions.index') }}" class="btn btn-danger">مسح</a>
                                </form>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle table-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>اسم المنصب</th>
                                            <th>الكود</th>
                                            <th>القسم</th>
                                            <th>الراتب الأدنى</th>
                                            <th>الراتب الأقصى</th>
                                            <th>عدد الموظفين</th>
                                            <th>الحالة</th>
                                            <th>العمليات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($positions as $position)
                                            <tr>
                                                <th>{{ $loop->iteration }}</th>
                                                <td>
                                                    <strong>{{ $position->title }}</strong>
                                                    @if ($position->description)
                                                        <br><small class="text-muted">{{ Str::limit($position->description, 50) }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($position->code)
                                                        <span class="badge bg-info">{{ $position->code }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($position->department)
                                                        <span class="badge bg-primary">{{ $position->department->name }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($position->min_salary)
                                                        {{ number_format($position->min_salary, 2) }} ر.س
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($position->max_salary)
                                                        {{ number_format($position->max_salary, 2) }} ر.س
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">{{ $position->employees_count ?? 0 }}</span>
                                                </td>
                                                <td>
                                                    @if ($position->is_active)
                                                        <span class="badge bg-success">نشط</span>
                                                    @else
                                                        <span class="badge bg-danger">غير نشط</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @can('position-show')
                                                    <a class="btn btn-info btn-sm me-1" href="{{ route('admin.positions.show', $position->id) }}" title="عرض">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                    @endcan
                                                    @can('position-edit')
                                                    <a class="btn btn-warning btn-sm me-1" href="{{ route('admin.positions.edit', $position->id) }}" title="تعديل">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </a>
                                                    @endcan
                                                    @can('position-delete')
                                                    <a class="btn btn-danger btn-sm me-1" data-bs-toggle="modal" data-bs-target="#delete{{ $position->id }}" title="حذف">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </a>
                                                    @endcan
                                                </td>
                                            </tr>
                                            @include('admin.pages.positions.delete')
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center text-danger fw-bold">لا توجد بيانات متاحة</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <div class="mt-3">
                                    {{ $positions->withQueryString()->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
@stop

