@extends('admin.layouts.master')

@section('page-title')
    تفاصيل تعيين المناوبة
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header d-flex justify-content-between align-items-center my-4">
                <h5 class="page-title mb-0">تفاصيل تعيين المناوبة</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.shift-assignments.index') }}" class="btn btn-secondary btn-sm">رجوع</a>
                    @can('shift-assignment-edit')
                    <a href="{{ route('admin.shift-assignments.edit', $assignment->id) }}" class="btn btn-primary btn-sm">تعديل</a>
                    @endcan
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات التعيين</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="200">الموظف</th>
                                    <td>{{ $assignment->employee->full_name }}</td>
                                </tr>
                                <tr>
                                    <th>المناوبة</th>
                                    <td>{{ $assignment->shift->name_ar ?? $assignment->shift->name }}</td>
                                </tr>
                                <tr>
                                    <th>وقت البدء</th>
                                    <td>{{ \Carbon\Carbon::parse($assignment->shift->start_time)->format('H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>وقت الانتهاء</th>
                                    <td>{{ \Carbon\Carbon::parse($assignment->shift->end_time)->format('H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>تاريخ البدء</th>
                                    <td>{{ $assignment->start_date->format('Y-m-d') }}</td>
                                </tr>
                                <tr>
                                    <th>تاريخ الانتهاء</th>
                                    <td>{{ $assignment->end_date ? $assignment->end_date->format('Y-m-d') : 'دائم' }}</td>
                                </tr>
                                <tr>
                                    <th>الحالة</th>
                                    <td>
                                        <span class="badge bg-{{ $assignment->is_active ? 'success' : 'secondary' }}">
                                            {{ $assignment->is_active ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </td>
                                </tr>
                                @if($assignment->notes)
                                <tr>
                                    <th>ملاحظات</th>
                                    <td>{{ $assignment->notes }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <th>تم التعيين بواسطة</th>
                                    <td>{{ $assignment->assignedBy->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>تاريخ الإنشاء</th>
                                    <td>{{ $assignment->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

