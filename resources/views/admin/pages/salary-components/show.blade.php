@extends('admin.layouts.master')

@section('page-title')
    تفاصيل مكون الراتب
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header d-flex justify-content-between align-items-center my-4">
                <h5 class="page-title mb-0">تفاصيل مكون الراتب</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.salary-components.index') }}" class="btn btn-secondary btn-sm">رجوع</a>
                    @can('salary-component-edit')
                    <a href="{{ route('admin.salary-components.edit', $component->id) }}" class="btn btn-primary btn-sm">تعديل</a>
                    @endcan
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">معلومات المكون</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="200">الكود</th>
                                    <td>{{ $component->code }}</td>
                                </tr>
                                <tr>
                                    <th>الاسم</th>
                                    <td>{{ $component->name }}</td>
                                </tr>
                                <tr>
                                    <th>الاسم بالعربية</th>
                                    <td>{{ $component->name_ar ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>النوع</th>
                                    <td>
                                        <span class="badge bg-{{ match($component->type) {
                                            'allowance' => 'success',
                                            'deduction' => 'danger',
                                            'bonus' => 'info',
                                            'overtime' => 'warning',
                                            default => 'secondary'
                                        } }}">
                                            {{ $component->type_name_ar }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>طريقة الحساب</th>
                                    <td>{{ $component->calculation_type_name_ar }}</td>
                                </tr>
                                @if($component->calculation_type == 'percentage')
                                <tr>
                                    <th>النسبة المئوية</th>
                                    <td>{{ $component->percentage }}%</td>
                                </tr>
                                @elseif($component->calculation_type == 'formula')
                                <tr>
                                    <th>الصيغة</th>
                                    <td><code>{{ $component->formula }}</code></td>
                                </tr>
                                @else
                                <tr>
                                    <th>القيمة الافتراضية</th>
                                    <td>{{ number_format($component->default_value, 2) }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <th>خاضع للضريبة</th>
                                    <td>{{ $component->is_taxable ? 'نعم' : 'لا' }}</td>
                                </tr>
                                <tr>
                                    <th>إلزامي</th>
                                    <td>{{ $component->is_required ? 'نعم' : 'لا' }}</td>
                                </tr>
                                <tr>
                                    <th>يطبق على جميع الموظفين</th>
                                    <td>{{ $component->apply_to_all ? 'نعم' : 'لا' }}</td>
                                </tr>
                                <tr>
                                    <th>الحالة</th>
                                    <td>
                                        <span class="badge bg-{{ $component->is_active ? 'success' : 'secondary' }}">
                                            {{ $component->is_active ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </td>
                                </tr>
                                @if($component->description)
                                <tr>
                                    <th>الوصف</th>
                                    <td>{{ $component->description }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

