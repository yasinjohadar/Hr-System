@extends('admin.layouts.master')

@section('page-title')
    قائمة أنواع المزايا
@stop

@section('content')
    @if (\Session::has('success'))
        <div class="alert alert-success">
            <ul>
                <li>{!! \Session::get('success') !!}</li>
            </ul>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">كافة أنواع المزايا</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex gap-3">
                            @can('benefit-type-create')
                            <a href="{{ route('admin.benefit-types.create') }}" class="btn btn-primary btn-sm">إضافة نوع ميزة جديد</a>
                            @endcan

                            <div class="flex-shrink-0">
                                <form action="{{ route('admin.benefit-types.index') }}" method="GET" class="d-flex align-items-center gap-2">
                                    <input style="width: 300px" type="text" name="search" class="form-control"
                                        placeholder="بحث" value="{{ request('search') }}">
                                    <select name="type" class="form-select">
                                        <option value="">كل الأنواع</option>
                                        <option value="monetary" {{ request('type') == 'monetary' ? 'selected' : '' }}>نقدي</option>
                                        <option value="in_kind" {{ request('type') == 'in_kind' ? 'selected' : '' }}>عيني</option>
                                        <option value="service" {{ request('type') == 'service' ? 'selected' : '' }}>خدمة</option>
                                        <option value="insurance" {{ request('type') == 'insurance' ? 'selected' : '' }}>تأمين</option>
                                        <option value="allowance" {{ request('type') == 'allowance' ? 'selected' : '' }}>بدل</option>
                                    </select>
                                    <button type="submit" class="btn btn-secondary">بحث</button>
                                    <a href="{{ route('admin.benefit-types.index') }}" class="btn btn-danger">مسح</a>
                                </form>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle table-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>الاسم</th>
                                            <th>الكود</th>
                                            <th>النوع</th>
                                            <th>القيمة الافتراضية</th>
                                            <th>عدد الموظفين</th>
                                            <th>الحالة</th>
                                            <th>العمليات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($benefitTypes as $benefitType)
                                            <tr>
                                                <th>{{ $loop->iteration }}</th>
                                                <td>
                                                    <strong>{{ $benefitType->name_ar ?? $benefitType->name }}</strong>
                                                </td>
                                                <td><span class="badge bg-info">{{ $benefitType->code }}</span></td>
                                                <td><span class="badge bg-secondary">{{ $benefitType->type_name_ar }}</span></td>
                                                <td>
                                                    @if ($benefitType->default_value)
                                                        {{ number_format($benefitType->default_value, 2) }}
                                                        @if ($benefitType->currency)
                                                            {{ $benefitType->currency->symbol_ar ?? $benefitType->currency->symbol }}
                                                        @endif
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td><span class="badge bg-success">{{ $benefitType->employee_benefits_count ?? 0 }}</span></td>
                                                <td>
                                                    @if ($benefitType->is_active)
                                                        <span class="badge bg-success">نشط</span>
                                                    @else
                                                        <span class="badge bg-danger">غير نشط</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @can('benefit-type-show')
                                                    <a class="btn btn-info btn-sm me-1" href="{{ route('admin.benefit-types.show', $benefitType->id) }}" title="عرض">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                    @endcan
                                                    @can('benefit-type-edit')
                                                    <a class="btn btn-warning btn-sm me-1" href="{{ route('admin.benefit-types.edit', $benefitType->id) }}" title="تعديل">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </a>
                                                    @endcan
                                                    @can('benefit-type-delete')
                                                    <a class="btn btn-danger btn-sm me-1" data-bs-toggle="modal" data-bs-target="#delete{{ $benefitType->id }}" title="حذف">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </a>
                                                    @endcan
                                                </td>
                                            </tr>
                                            @include('admin.pages.benefit-types.delete')
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-danger fw-bold">لا توجد بيانات متاحة</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <div class="mt-3">
                                    {{ $benefitTypes->withQueryString()->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop


