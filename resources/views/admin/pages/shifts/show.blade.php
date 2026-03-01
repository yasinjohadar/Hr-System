@extends('admin.layouts.master')

@section('page-title')
    تفاصيل المناوبة
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header d-flex justify-content-between align-items-center my-4">
                <h5 class="page-title mb-0">تفاصيل المناوبة - {{ $shift->name_ar ?? $shift->name }}</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.shifts.index') }}" class="btn btn-secondary btn-sm">رجوع</a>
                    @can('shift-edit')
                    <a href="{{ route('admin.shifts.edit', $shift->id) }}" class="btn btn-primary btn-sm">تعديل</a>
                    @endcan
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات المناوبة</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="200">الكود</th>
                                    <td>{{ $shift->shift_code }}</td>
                                </tr>
                                <tr>
                                    <th>الاسم</th>
                                    <td>{{ $shift->name }}</td>
                                </tr>
                                <tr>
                                    <th>الاسم بالعربية</th>
                                    <td>{{ $shift->name_ar ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>وقت البدء</th>
                                    <td>{{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>وقت الانتهاء</th>
                                    <td>{{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>المدة (ساعات)</th>
                                    <td>{{ $shift->duration_hours }}</td>
                                </tr>
                                <tr>
                                    <th>فترة السماح للتأخير</th>
                                    <td>{{ $shift->grace_period_minutes }} دقيقة</td>
                                </tr>
                                <tr>
                                    <th>مدة الاستراحة</th>
                                    <td>{{ $shift->break_duration_minutes }} دقيقة</td>
                                </tr>
                                <tr>
                                    <th>معدل الساعات الإضافية</th>
                                    <td>{{ $shift->overtime_rate }}x</td>
                                </tr>
                                <tr>
                                    <th>الحد الأدنى للساعات الإضافية</th>
                                    <td>{{ $shift->overtime_threshold_minutes }} دقيقة</td>
                                </tr>
                                <tr>
                                    <th>أيام العمل</th>
                                    <td>
                                        @if($shift->monday) الاثنين @endif
                                        @if($shift->tuesday) الثلاثاء @endif
                                        @if($shift->wednesday) الأربعاء @endif
                                        @if($shift->thursday) الخميس @endif
                                        @if($shift->friday) الجمعة @endif
                                        @if($shift->saturday) السبت @endif
                                        @if($shift->sunday) الأحد @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>الحالة</th>
                                    <td>
                                        <span class="badge bg-{{ $shift->is_active ? 'success' : 'secondary' }}">
                                            {{ $shift->is_active ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </td>
                                </tr>
                                @if($shift->description)
                                <tr>
                                    <th>الوصف</th>
                                    <td>{{ $shift->description }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    @if($shift->assignments->count() > 0)
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">الموظفون المعينون ({{ $shift->assignments->count() }})</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>الموظف</th>
                                            <th>تاريخ البدء</th>
                                            <th>تاريخ الانتهاء</th>
                                            <th>الحالة</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($shift->assignments as $assignment)
                                            <tr>
                                                <td>{{ $assignment->employee->full_name }}</td>
                                                <td>{{ $assignment->start_date->format('Y-m-d') }}</td>
                                                <td>{{ $assignment->end_date ? $assignment->end_date->format('Y-m-d') : 'دائم' }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $assignment->is_active ? 'success' : 'secondary' }}">
                                                        {{ $assignment->is_active ? 'نشط' : 'غير نشط' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

