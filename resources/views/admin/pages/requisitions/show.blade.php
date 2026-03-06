@extends('admin.layouts.master')

@section('page-title')
    تفاصيل طلب التعيين
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">طلب تعيين: {{ $requisition->requisition_code }}</h5>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.requisitions.index') }}" class="btn btn-secondary btn-sm">عودة</a>
                    @if ($requisition->status === 'pending')
                        @can('requisition-edit')
                        <a href="{{ route('admin.requisitions.edit', $requisition) }}" class="btn btn-primary btn-sm">تعديل</a>
                        @endcan
                        @can('requisition-approve')
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#approveModal">موافقة</button>
                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal">رفض</button>
                        @endcan
                    @endif
                    @if ($requisition->jobVacancy)
                        <a href="{{ route('admin.job-vacancies.show', $requisition->jobVacancy) }}" class="btn btn-info btn-sm">عرض الشاغر الوظيفي</a>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">بيانات الطلب</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="180">كود الطلب</th>
                                    <td>{{ $requisition->requisition_code }}</td>
                                </tr>
                                <tr>
                                    <th>القسم</th>
                                    <td>{{ $requisition->department->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>المنصب</th>
                                    <td>{{ $requisition->position->title ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>الفرع</th>
                                    <td>{{ $requisition->branch->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>عدد المناصب المطلوبة</th>
                                    <td>{{ $requisition->number_of_positions }}</td>
                                </tr>
                                <tr>
                                    <th>الحالة</th>
                                    <td>
                                        <span class="badge bg-{{ $requisition->status == 'approved' ? 'success' : ($requisition->status == 'rejected' ? 'danger' : 'warning') }}">
                                            {{ $requisition->status_name_ar }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>المبرر</th>
                                    <td>{{ $requisition->justification }}</td>
                                </tr>
                                @if ($requisition->notes)
                                <tr>
                                    <th>ملاحظات</th>
                                    <td>{{ $requisition->notes }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <th>تاريخ الإنشاء</th>
                                    <td>{{ $requisition->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>أنشئ بواسطة</th>
                                    <td>{{ $requisition->creator->name ?? '-' }}</td>
                                </tr>
                                @if ($requisition->status !== 'pending')
                                <tr>
                                    <th>تاريخ الموافقة/الرفض</th>
                                    <td>{{ $requisition->approved_at?->format('Y-m-d H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>بواسطة</th>
                                    <td>{{ $requisition->approvedBy->name ?? '-' }}</td>
                                </tr>
                                @if ($requisition->rejection_reason)
                                <tr>
                                    <th>سبب الرفض</th>
                                    <td>{{ $requisition->rejection_reason }}</td>
                                </tr>
                                @endif
                                @endif
                                @if ($requisition->jobVacancy)
                                <tr>
                                    <th>الشاغر المرتبط</th>
                                    <td>
                                        <a href="{{ route('admin.job-vacancies.show', $requisition->jobVacancy) }}">{{ $requisition->jobVacancy->code }} - {{ $requisition->jobVacancy->title }}</a>
                                    </td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @can('requisition-approve')
    @if ($requisition->status === 'pending')
    <div class="modal fade" id="approveModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">موافقة على طلب التعيين</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>سيتم إنشاء شاغر وظيفي مرتبط بهذا الطلب. هل أنت متأكد من الموافقة؟</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <form action="{{ route('admin.requisitions.approve', $requisition) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success">موافقة</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">رفض طلب التعيين</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.requisitions.reject', $requisition) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <label class="form-label">سبب الرفض (اختياري)</label>
                        <textarea name="rejection_reason" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-danger">رفض</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
    @endcan
@stop
