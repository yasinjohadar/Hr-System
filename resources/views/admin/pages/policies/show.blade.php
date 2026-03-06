@extends('admin.layouts.master')

@section('page-title')
    عرض السياسة
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('info'))
        <div class="alert alert-info alert-dismissible fade show">
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">عرض السياسة</h5>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.policies.index') }}" class="btn btn-secondary btn-sm">عودة</a>
                    <a href="{{ route('admin.policies.edit', $policy) }}" class="btn btn-warning btn-sm">تعديل</a>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">{{ $policy->title }}</h5>
                            @if ($policy->is_active)
                                <span class="badge bg-success">نشط</span>
                            @else
                                <span class="badge bg-secondary">غير نشط</span>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>التصنيف:</strong> {{ $policy->category ?? '—' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>الإصدار:</strong> {{ $policy->version ?? '—' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>تاريخ السريان:</strong> {{ $policy->effective_date?->format('Y-m-d') ?? '—' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>أنشئ بواسطة:</strong> {{ $policy->creator->name ?? '—' }}</p>
                                </div>
                            </div>
                            @if ($policy->document_path)
                                <p class="mb-2"><strong>المرفق:</strong> <a href="{{ asset('storage/' . $policy->document_path) }}" target="_blank">تحميل</a></p>
                            @endif
                            <hr>
                            <div class="policy-content">
                                {!! nl2br(e($policy->content)) !!}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <!-- تسجيل اعتراف موظف -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">تسجيل اعتراف موظف</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.policies.acknowledge', $policy) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">الموظف <span class="text-danger">*</span></label>
                                    <select name="employee_id" class="form-select" required>
                                        <option value="">اختر الموظف</option>
                                        @foreach ($employees as $emp)
                                            <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->employee_code ?? $emp->id }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary w-100"><i class="fa fa-check me-2"></i>تسجيل الاعتراف</button>
                            </form>
                        </div>
                    </div>

                    <!-- قائمة المعترفين -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">سجل الاعترافات ({{ $policy->acknowledgments->count() }})</h5>
                        </div>
                        <div class="card-body p-0">
                            @if ($policy->acknowledgments->isEmpty())
                                <p class="text-muted text-center py-4 mb-0">لا يوجد اعترافات حتى الآن.</p>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>الموظف</th>
                                                <th>تاريخ الاعتراف</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($policy->acknowledgments as $ack)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('admin.employees.show', $ack->employee_id) }}">{{ $ack->employee->full_name ?? $ack->employee->employee_code }}</a>
                                                    </td>
                                                    <td>{{ $ack->acknowledged_at->format('Y-m-d H:i') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
