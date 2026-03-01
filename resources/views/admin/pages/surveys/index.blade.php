@extends('admin.layouts.master')

@section('page-title')
    الاستبيانات
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">الاستبيانات</h5>
                </div>
                <div>
                    @can('survey-create')
                    <a href="{{ route('admin.surveys.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>إنشاء استبيان جديد
                    </a>
                    @endcan
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.surveys.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="بحث..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">كل الحالات</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>مغلق</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="type" class="form-select">
                                <option value="">كل الأنواع</option>
                                <option value="satisfaction" {{ request('type') == 'satisfaction' ? 'selected' : '' }}>رضا</option>
                                <option value="climate" {{ request('type') == 'climate' ? 'selected' : '' }}>مناخ عمل</option>
                                <option value="engagement" {{ request('type') == 'engagement' ? 'selected' : '' }}>مشاركة</option>
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
                    <h5 class="card-title mb-0">قائمة الاستبيانات ({{ $surveys->total() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>كود الاستبيان</th>
                                    <th>العنوان</th>
                                    <th>النوع</th>
                                    <th>تاريخ البدء</th>
                                    <th>تاريخ الانتهاء</th>
                                    <th>عدد الردود</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($surveys as $survey)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $survey->survey_code }}</td>
                                        <td>{{ $survey->title_ar ?? $survey->title }}</td>
                                        <td><span class="badge bg-info">{{ $survey->type_name_ar }}</span></td>
                                        <td>{{ $survey->start_date->format('Y-m-d') }}</td>
                                        <td>{{ $survey->end_date->format('Y-m-d') }}</td>
                                        <td>{{ $survey->responses_count }}</td>
                                        <td>
                                            <span class="badge bg-{{ $survey->status == 'active' ? 'success' : ($survey->status == 'closed' ? 'secondary' : 'warning') }}">
                                                {{ $survey->status_name_ar }}
                                            </span>
                                        </td>
                                        <td>
                                            @can('survey-show')
                                            <a href="{{ route('admin.surveys.show', $survey->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">لا توجد استبيانات</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $surveys->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

