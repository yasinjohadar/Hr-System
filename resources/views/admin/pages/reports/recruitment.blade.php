@extends('admin.layouts.master')

@section('page-title')
    تقرير التوظيف
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تقرير التوظيف</h5>
                </div>
                <div>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للتقارير
                    </a>
                </div>
            </div>

            <!-- الإحصائيات -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h6 class="card-title">الوظائف الشاغرة</h6>
                            <h2 class="mb-0">{{ $stats['total_vacancies'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6 class="card-title">منشورة</h6>
                            <h2 class="mb-0">{{ $stats['published_vacancies'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h6 class="card-title">طلبات التوظيف</h6>
                            <h2 class="mb-0">{{ $stats['total_applications'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h6 class="card-title">تم التوظيف</h6>
                            <h2 class="mb-0">{{ $stats['hired'] }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الوظائف الشاغرة -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">الوظائف الشاغرة ({{ $vacancies->count() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الوظيفة</th>
                                    <th>القسم</th>
                                    <th>عدد المناصب</th>
                                    <th>المتقدمون</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($vacancies as $vacancy)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong>{{ $vacancy->title_ar ?? $vacancy->title }}</strong></td>
                                        <td>{{ $vacancy->department->name ?? '-' }}</td>
                                        <td><span class="badge bg-info">{{ $vacancy->number_of_positions }}</span></td>
                                        <td><span class="badge bg-warning">{{ $vacancy->applications_count ?? 0 }}</span></td>
                                        <td><span class="badge bg-{{ $vacancy->status == 'published' ? 'success' : 'secondary' }}">{{ $vacancy->status_name_ar }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- طلبات التوظيف -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">طلبات التوظيف ({{ $applications->count() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>المرشح</th>
                                    <th>الوظيفة</th>
                                    <th>تاريخ التقديم</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($applications as $application)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong>{{ $application->candidate->full_name }}</strong></td>
                                        <td>{{ $application->jobVacancy->title_ar ?? $application->jobVacancy->title }}</td>
                                        <td>{{ $application->application_date->format('Y-m-d') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $application->status == 'accepted' ? 'success' : ($application->status == 'rejected' ? 'danger' : 'warning') }}">
                                                {{ $application->status_name_ar }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- المقابلات -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">المقابلات ({{ $interviews->count() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>المرشح</th>
                                    <th>الوظيفة</th>
                                    <th>تاريخ المقابلة</th>
                                    <th>نوع المقابلة</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($interviews as $interview)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong>{{ $interview->candidate->full_name }}</strong></td>
                                        <td>{{ $interview->jobVacancy->title_ar ?? $interview->jobVacancy->title }}</td>
                                        <td>{{ $interview->interview_date->format('Y-m-d') }}</td>
                                        <td><span class="badge bg-info">{{ $interview->type_name_ar }}</span></td>
                                        <td>
                                            <span class="badge bg-{{ $interview->status == 'completed' ? 'success' : 'warning' }}">
                                                {{ $interview->status_name_ar }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop


