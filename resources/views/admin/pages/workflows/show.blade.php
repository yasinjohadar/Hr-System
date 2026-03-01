@extends('admin.layouts.master')

@section('page-title')
    تفاصيل سير العمل
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل سير العمل</h5>
                </div>
                <div>
                    @can('workflow-edit')
                    <a href="{{ route('admin.workflows.edit', $workflow->id) }}" class="btn btn-info">
                        <i class="fas fa-edit me-2"></i>تعديل
                    </a>
                    @endcan
                    <a href="{{ route('admin.workflows.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات سير العمل</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="200">الاسم</th>
                                    <td>{{ $workflow->name_ar ?? $workflow->name }}</td>
                                </tr>
                                <tr>
                                    <th>الكود</th>
                                    <td>{{ $workflow->code ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>النوع</th>
                                    <td><span class="badge bg-info">{{ $workflow->type_name_ar }}</span></td>
                                </tr>
                                <tr>
                                    <th>الوصف</th>
                                    <td>{{ $workflow->description ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>الحالة</th>
                                    <td>
                                        <span class="badge bg-{{ $workflow->is_active ? 'success' : 'danger' }}">
                                            {{ $workflow->is_active ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>عدد الخطوات</th>
                                    <td>{{ $workflow->steps_count }}</td>
                                </tr>
                                <tr>
                                    <th>عدد الطلبات</th>
                                    <td>{{ $workflow->instances_count }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

