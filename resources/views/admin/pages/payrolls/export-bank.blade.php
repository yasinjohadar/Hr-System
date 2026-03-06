@extends('admin.layouts.master')

@section('page-title')
    تصدير ملف الرواتب للبنك
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تصدير ملف الرواتب للبنك</h5>
                </div>
                <a href="{{ route('admin.payrolls.index') }}" class="btn btn-secondary btn-sm">عودة</a>
            </div>

            <div class="row">
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">اختيار الدفعة</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">اختر الشهر والسنة لتصدير كشوف الرواتب المعتمدة أو المدفوعة إلى ملف CSV للتحويل البنكي.</p>
                            <form action="{{ route('admin.payrolls.export-bank-file') }}" method="GET" class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">الشهر</label>
                                    <select name="month" class="form-select" required>
                                        @for ($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}" {{ (request('month') ?: date('n')) == $i ? 'selected' : '' }}>
                                                {{ ['', 'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'][$i] }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">السنة</label>
                                    <input type="number" name="year" class="form-control" min="2020" max="2030" value="{{ request('year', date('Y')) }}" required>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-download me-2"></i>تحميل ملف CSV للبنك
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
