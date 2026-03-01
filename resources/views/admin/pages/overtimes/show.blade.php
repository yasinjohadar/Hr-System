@extends('admin.layouts.master')

@section('page-title')
    تفاصيل الساعات الإضافية
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header d-flex justify-content-between align-items-center my-4">
                <h5 class="page-title mb-0">تفاصيل الساعات الإضافية</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.overtimes.index') }}" class="btn btn-secondary btn-sm">رجوع</a>
                    @can('overtime-edit')
                    @if($overtime->status != 'paid')
                    <a href="{{ route('admin.overtimes.edit', $overtime->id) }}" class="btn btn-primary btn-sm">تعديل</a>
                    @endif
                    @endcan
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات الساعات الإضافية</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr><th width="200">الموظف</th><td>{{ $overtime->employee->full_name }}</td></tr>
                                <tr><th>التاريخ</th><td>{{ $overtime->overtime_date->format('Y-m-d') }}</td></tr>
                                <tr><th>من</th><td>{{ \Carbon\Carbon::parse($overtime->start_time)->format('H:i') }}</td></tr>
                                <tr><th>إلى</th><td>{{ \Carbon\Carbon::parse($overtime->end_time)->format('H:i') }}</td></tr>
                                <tr><th>الساعات الإضافية</th><td>{{ number_format($overtime->overtime_hours, 2) }} ساعة</td></tr>
                                <tr><th>النوع</th><td><span class="badge bg-info">{{ $overtime->overtime_type_name_ar }}</span></td></tr>
                                <tr><th>معدل الضرب</th><td>{{ $overtime->rate_multiplier }}x</td></tr>
                                <tr><th>معدل الساعة</th><td>{{ number_format($overtime->hourly_rate, 2) }}</td></tr>
                                <tr><th>المبلغ</th><td><strong>{{ number_format($overtime->overtime_amount, 2) }}</strong></td></tr>
                                <tr><th>الحالة</th><td><span class="badge bg-{{ match($overtime->status) {'pending'=>'warning','approved'=>'success','rejected'=>'danger','paid'=>'primary',default=>'secondary'} }}">{{ $overtime->status_name_ar }}</span></td></tr>
                                @if($overtime->approved_by)
                                <tr><th>تمت الموافقة من</th><td>{{ $overtime->approvedBy->name ?? '-' }} في {{ $overtime->approved_at->format('Y-m-d H:i') }}</td></tr>
                                @endif
                                @if($overtime->reason)
                                <tr><th>السبب</th><td>{{ $overtime->reason }}</td></tr>
                                @endif
                                @if($overtime->approval_notes)
                                <tr><th>ملاحظات الموافقة</th><td>{{ $overtime->approval_notes }}</td></tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    @if($overtime->status == 'pending')
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">الإجراءات</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.overtimes.approve', $overtime->id) }}" method="POST" class="d-inline">
                                @csrf
                                <div class="mb-2">
                                    <label class="form-label">ملاحظات الموافقة</label>
                                    <textarea class="form-control" name="approval_notes" rows="2"></textarea>
                                </div>
                                <button type="submit" class="btn btn-success">موافقة</button>
                            </form>
                            <form action="{{ route('admin.overtimes.reject', $overtime->id) }}" method="POST" class="d-inline">
                                @csrf
                                <div class="mb-2">
                                    <label class="form-label">ملاحظات الرفض</label>
                                    <textarea class="form-control" name="approval_notes" rows="2"></textarea>
                                </div>
                                <button type="submit" class="btn btn-danger">رفض</button>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop

