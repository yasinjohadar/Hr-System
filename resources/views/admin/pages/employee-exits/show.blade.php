@extends('admin.layouts.master')

@section('page-title')
    تفاصيل إنهاء الخدمة
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل إنهاء الخدمة</h5>
                </div>
                <div>
                    <a href="{{ route('admin.employee-exits.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">طلب إنهاء خدمة - {{ $exit->employee->full_name }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الموظف:</label>
                                    <p class="form-control-plaintext">{{ $exit->employee->full_name }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">نوع إنهاء الخدمة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-info">{{ $exit->exit_type_name_ar }}</span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تاريخ الاستقالة:</label>
                                    <p class="form-control-plaintext">{{ $exit->resignation_date->format('Y-m-d') }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">آخر يوم عمل:</label>
                                    <p class="form-control-plaintext">{{ $exit->last_working_day->format('Y-m-d') }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الحالة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $exit->status == 'completed' ? 'success' : ($exit->status == 'in_process' ? 'primary' : 'warning') }}">
                                            {{ $exit->status_name_ar }}
                                        </span>
                                    </p>
                                </div>
                                @if ($exit->reason)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">السبب:</label>
                                    <p class="form-control-plaintext">{{ $exit->reason_ar ?? $exit->reason }}</p>
                                </div>
                                @endif
                            </div>

                            <!-- حالة العملية -->
                            <div class="card mt-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">حالة العملية</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 mb-2">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-{{ $exit->exit_interview_completed ? 'check-circle text-success' : 'times-circle text-danger' }} me-2"></i>
                                                <span>استبيان إنهاء الخدمة</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-{{ $exit->assets_returned ? 'check-circle text-success' : 'times-circle text-danger' }} me-2"></i>
                                                <span>استرجاع الأصول</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-{{ $exit->handover_completed ? 'check-circle text-success' : 'times-circle text-danger' }} me-2"></i>
                                                <span>تسليم المهام</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-{{ $exit->documents_returned ? 'check-circle text-success' : 'times-circle text-danger' }} me-2"></i>
                                                <span>استرجاع المستندات</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-{{ $exit->final_settlement_completed ? 'check-circle text-success' : 'times-circle text-danger' }} me-2"></i>
                                                <span>التسوية النهائية</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if (!$exit->exit_interview_completed)
                            <div class="card mt-3">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">استبيان إنهاء الخدمة</h6>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="{{ route('admin.employee-exits.complete-interview', $exit->id) }}">
                                        @csrf
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <label class="form-label">التقييم (1-5) <span class="text-danger">*</span></label>
                                                <select name="exit_interview_rating" class="form-select" required>
                                                    <option value="1">1 - سيء جداً</option>
                                                    <option value="2">2 - سيء</option>
                                                    <option value="3">3 - متوسط</option>
                                                    <option value="4">4 - جيد</option>
                                                    <option value="5">5 - ممتاز</option>
                                                </select>
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label">ملاحظات</label>
                                                <textarea name="exit_interview_feedback" class="form-control" rows="3"></textarea>
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label">اقتراحات</label>
                                                <textarea name="suggestions" class="form-control" rows="3"></textarea>
                                            </div>
                                            <div class="col-md-12">
                                                <button type="submit" class="btn btn-primary">إكمال الاستبيان</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @endif

                            <div class="mt-3">
                                @can('employee-exit-edit')
                                <a href="{{ route('admin.employee-exits.edit', $exit->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-2"></i>تعديل
                                </a>
                                @endcan
                                @if ($exit->status == 'pending')
                                <a href="{{ route('admin.employee-exits.approve', $exit->id) }}" class="btn btn-success">
                                    <i class="fas fa-check me-2"></i>موافقة
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop


