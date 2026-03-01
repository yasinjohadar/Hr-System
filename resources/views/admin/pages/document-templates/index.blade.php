@extends('admin.layouts.master')

@section('page-title')
    قوالب المستندات
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">قوالب المستندات</h5>
                </div>
                <div>
                    @can('document-template-create')
                    <a href="{{ route('admin.document-templates.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>إضافة قالب جديد
                    </a>
                    @endcan
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.document-templates.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="بحث..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="type" class="form-select">
                                <option value="">كل الأنواع</option>
                                <option value="contract" {{ request('type') == 'contract' ? 'selected' : '' }}>عقد عمل</option>
                                <option value="letter" {{ request('type') == 'letter' ? 'selected' : '' }}>خطاب</option>
                                <option value="certificate" {{ request('type') == 'certificate' ? 'selected' : '' }}>شهادة</option>
                                <option value="report" {{ request('type') == 'report' ? 'selected' : '' }}>تقرير</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="is_active" class="form-select">
                                <option value="">كل الحالات</option>
                                <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>نشط</option>
                                <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>غير نشط</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">تطبيق</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">قائمة القوالب ({{ $templates->total() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الاسم</th>
                                    <th>الكود</th>
                                    <th>النوع</th>
                                    <th>صيغة الملف</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($templates as $template)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong>{{ $template->name_ar ?? $template->name }}</strong></td>
                                        <td>{{ $template->code }}</td>
                                        <td><span class="badge bg-info">{{ $template->type_name_ar }}</span></td>
                                        <td><span class="badge bg-secondary">{{ strtoupper($template->file_format) }}</span></td>
                                        <td>
                                            <span class="badge bg-{{ $template->is_active ? 'success' : 'danger' }}">
                                                {{ $template->is_active ? 'نشط' : 'غير نشط' }}
                                            </span>
                                        </td>
                                        <td>
                                            @can('document-template-show')
                                            <a href="{{ route('admin.document-templates.show', $template->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            @can('document-template-edit')
                                            <a href="{{ route('admin.document-templates.edit', $template->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">لا توجد قوالب</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $templates->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

