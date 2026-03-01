@extends('admin.layouts.master')

@section('page-title')
    قوالب عملية الاستقبال
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">قوالب عملية الاستقبال</h5>
                </div>
                <div>
                    @can('onboarding-template-create')
                    <a href="{{ route('admin.onboarding-templates.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>إضافة قالب جديد
                    </a>
                    @endcan
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.onboarding-templates.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="بحث..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="type" class="form-select">
                                <option value="">كل الأنواع</option>
                                <option value="standard" {{ request('type') == 'standard' ? 'selected' : '' }}>قياسي</option>
                                <option value="executive" {{ request('type') == 'executive' ? 'selected' : '' }}>تنفيذي</option>
                                <option value="contractor" {{ request('type') == 'contractor' ? 'selected' : '' }}>مقاول</option>
                                <option value="intern" {{ request('type') == 'intern' ? 'selected' : '' }}>متدرّب</option>
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
                                    <th>النوع</th>
                                    <th>عدد العمليات</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($templates as $template)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong>{{ $template->name_ar ?? $template->name }}</strong></td>
                                        <td><span class="badge bg-info">{{ $template->type_name_ar }}</span></td>
                                        <td>{{ $template->processes_count }}</td>
                                        <td>
                                            <span class="badge bg-{{ $template->is_active ? 'success' : 'danger' }}">
                                                {{ $template->is_active ? 'نشط' : 'غير نشط' }}
                                            </span>
                                        </td>
                                        <td>
                                            @can('onboarding-template-show')
                                            <a href="{{ route('admin.onboarding-templates.show', $template->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            @can('onboarding-template-edit')
                                            <a href="{{ route('admin.onboarding-templates.edit', $template->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">لا توجد قوالب</td>
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

