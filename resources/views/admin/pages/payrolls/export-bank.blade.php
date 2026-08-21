@extends('admin.layouts.master')

@section('page-title')
    تصدير ملف الرواتب للبنك
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">
            @include('admin.pages.users.partials.alerts')

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="ri-bank-line"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>تصدير ملف الرواتب للبنك</h1>
                        <p>اختر الشهر والسنة لتصدير الكشوف المعتمدة أو المدفوعة إلى ملف CSV</p>
                    </div>
                </div>
                <div class="admin-page-banner-actions">
                    <a href="{{ route('admin.payrolls.index') }}" class="admin-btn admin-btn-light">
                        <i class="ri-arrow-right-line"></i>
                        العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-6">
                    <div class="admin-page-card">
                        <div class="card-toolbar">
                            <h5 class="mb-0 fw-bold">اختيار الدفعة</h5>
                        </div>
                        <form class="admin-form" action="{{ route('admin.payrolls.export-bank-file') }}" method="GET">
                            <div class="admin-form-body">
                                <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="admin-form-label">الشهر</label>
                                    <select name="month" class="form-select" required>
                                        @for ($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}" {{ (request('month') ?: date('n')) == $i ? 'selected' : '' }}>
                                                {{ ['', 'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'][$i] }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="admin-form-label">السنة</label>
                                    <input type="number" name="year" class="form-control" min="2020" max="2030" value="{{ request('year', date('Y')) }}" required>
                                </div>
                                </div>
                            </div>

                            <div class="admin-form-footer">
                                <button type="submit" class="admin-btn admin-btn-primary">
                                    <i class="ri-download-2-line"></i>
                                    تحميل ملف CSV للبنك
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
