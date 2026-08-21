@extends('admin.layouts.master')

@section('page-title')
    تفاصيل مكون الراتب
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">
            @include('admin.pages.users.partials.alerts')

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="ri-list-settings-line"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>تفاصيل مكوّن الراتب</h1>
                        <p>{{ $component->name_ar ?? $component->name }} — {{ $component->code }}</p>
                    </div>
                </div>
                <div class="admin-page-banner-actions">
                    @can('salary-component-edit')
                        <a href="{{ route('admin.salary-components.edit', $component->id) }}" class="admin-btn admin-btn-light">
                            <i class="ri-pencil-line"></i>
                            تعديل
                        </a>
                    @endcan
                    <a href="{{ route('admin.salary-components.index') }}" class="admin-btn admin-btn-secondary">
                        <i class="ri-arrow-right-line"></i>
                        العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="admin-page-card">
                        <div class="card-toolbar">
                            <h5 class="mb-0 fw-bold">معلومات المكون</h5>
                        </div>
                        <div class="admin-form-body">
                            <table class="admin-data-table admin-data-table-sm">
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
                                        <span class="admin-badge admin-badge-{{ match($component->type) {
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
                                        <span class="admin-badge admin-badge-{{ $component->is_active ? 'success' : 'muted' }}">
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

