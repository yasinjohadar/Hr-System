@extends('admin.layouts.master')

@section('page-title')
    تفاصيل قاعدة الحضور
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header d-flex justify-content-between align-items-center my-4">
                <h5 class="page-title mb-0">تفاصيل قاعدة الحضور</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.attendance-rules.index') }}" class="btn btn-secondary btn-sm">رجوع</a>
                    @can('attendance-rule-edit')
                    <a href="{{ route('admin.attendance-rules.edit', $rule->id) }}" class="btn btn-primary btn-sm">تعديل</a>
                    @endcan
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات القاعدة</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr><th width="200">الكود</th><td>{{ $rule->rule_code }}</td></tr>
                                <tr><th>الاسم</th><td>{{ $rule->name }}</td></tr>
                                <tr><th>الاسم بالعربية</th><td>{{ $rule->name_ar ?? '-' }}</td></tr>
                                <tr><th>نوع القاعدة</th><td><span class="badge bg-info">{{ $rule->rule_type_name_ar }}</span></td></tr>
                                <tr><th>الحد الأدنى</th><td>{{ $rule->threshold_minutes }} دقيقة</td></tr>
                                <tr><th>نوع الإجراء</th><td><span class="badge bg-warning">{{ $rule->action_type_name_ar }}</span></td></tr>
                                @if($rule->deduction_amount)
                                <tr><th>مبلغ الخصم</th><td>{{ number_format($rule->deduction_amount, 2) }}</td></tr>
                                @endif
                                @if($rule->deduction_percentage)
                                <tr><th>نسبة الخصم</th><td>{{ $rule->deduction_percentage }}%</td></tr>
                                @endif
                                <tr><th>الأولوية</th><td>{{ $rule->priority }}</td></tr>
                                <tr><th>يطبق على الجميع</th><td>{{ $rule->apply_to_all ? 'نعم' : 'لا' }}</td></tr>
                                <tr><th>إرسال إشعار</th><td>{{ $rule->send_notification ? 'نعم' : 'لا' }}</td></tr>
                                <tr><th>الحالة</th><td><span class="badge bg-{{ $rule->is_active ? 'success' : 'secondary' }}">{{ $rule->is_active ? 'نشط' : 'غير نشط' }}</span></td></tr>
                                @if($rule->description)
                                <tr><th>الوصف</th><td>{{ $rule->description }}</td></tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

