@extends('employee.layouts.master')

@section('page-title')
    المخالفات
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">المخالفات</h5>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">قائمة المخالفات ({{ $violations->total() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>كود المخالفة</th>
                                    <th>تاريخ المخالفة</th>
                                    <th>نوع المخالفة</th>
                                    <th>الإجراء التأديبي</th>
                                    <th>الخطورة</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($violations as $violation)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $violation->violation_code }}</td>
                                        <td>{{ $violation->violation_date->format('Y-m-d') }}</td>
                                        <td>{{ $violation->violationType->name_ar ?? $violation->violationType->name }}</td>
                                        <td>
                                            @if($violation->disciplinaryAction)
                                                {{ $violation->disciplinaryAction->name_ar ?? $violation->disciplinaryAction->name }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $violation->severity == 'critical' ? 'danger' : ($violation->severity == 'high' ? 'warning' : ($violation->severity == 'medium' ? 'info' : 'secondary')) }}">
                                                {{ $violation->severity_name_ar }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $violation->status == 'approved' ? 'success' : ($violation->status == 'rejected' ? 'danger' : ($violation->status == 'dismissed' ? 'info' : 'warning')) }}">
                                                {{ $violation->status_name_ar }}
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewModal{{ $violation->id }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Modal للتفاصيل -->
                                    <div class="modal fade" id="viewModal{{ $violation->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">تفاصيل المخالفة</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row mb-3">
                                                        <div class="col-md-4"><strong>كود المخالفة:</strong></div>
                                                        <div class="col-md-8">{{ $violation->violation_code }}</div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-md-4"><strong>تاريخ المخالفة:</strong></div>
                                                        <div class="col-md-8">{{ $violation->violation_date->format('Y-m-d') }}</div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-md-4"><strong>نوع المخالفة:</strong></div>
                                                        <div class="col-md-8">{{ $violation->violationType->name_ar ?? $violation->violationType->name }}</div>
                                                    </div>
                                                    @if($violation->disciplinaryAction)
                                                    <div class="row mb-3">
                                                        <div class="col-md-4"><strong>الإجراء التأديبي:</strong></div>
                                                        <div class="col-md-8">{{ $violation->disciplinaryAction->name_ar ?? $violation->disciplinaryAction->name }}</div>
                                                    </div>
                                                    @endif
                                                    <div class="row mb-3">
                                                        <div class="col-md-4"><strong>الخطورة:</strong></div>
                                                        <div class="col-md-8">
                                                            <span class="badge bg-{{ $violation->severity == 'critical' ? 'danger' : ($violation->severity == 'high' ? 'warning' : ($violation->severity == 'medium' ? 'info' : 'secondary')) }}">
                                                                {{ $violation->severity_name_ar }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-md-4"><strong>الحالة:</strong></div>
                                                        <div class="col-md-8">
                                                            <span class="badge bg-{{ $violation->status == 'approved' ? 'success' : ($violation->status == 'rejected' ? 'danger' : ($violation->status == 'dismissed' ? 'info' : 'warning')) }}">
                                                                {{ $violation->status_name_ar }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    @if($violation->description)
                                                    <div class="row mb-3">
                                                        <div class="col-md-4"><strong>الوصف:</strong></div>
                                                        <div class="col-md-8">{{ $violation->description }}</div>
                                                    </div>
                                                    @endif
                                                    @if($violation->description_ar)
                                                    <div class="row mb-3">
                                                        <div class="col-md-4"><strong>الوصف (عربي):</strong></div>
                                                        <div class="col-md-8">{{ $violation->description_ar }}</div>
                                                    </div>
                                                    @endif
                                                    @if($violation->investigation_notes)
                                                    <div class="row mb-3">
                                                        <div class="col-md-4"><strong>ملاحظات التحقيق:</strong></div>
                                                        <div class="col-md-8">{{ $violation->investigation_notes }}</div>
                                                    </div>
                                                    @endif
                                                    @if($violation->dismissal_reason)
                                                    <div class="row mb-3">
                                                        <div class="col-md-4"><strong>سبب الإلغاء:</strong></div>
                                                        <div class="col-md-8 text-info">{{ $violation->dismissal_reason }}</div>
                                                    </div>
                                                    @endif
                                                    @if($violation->notes)
                                                    <div class="row mb-3">
                                                        <div class="col-md-4"><strong>ملاحظات:</strong></div>
                                                        <div class="col-md-8">{{ $violation->notes }}</div>
                                                    </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">لا توجد مخالفات</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $violations->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

