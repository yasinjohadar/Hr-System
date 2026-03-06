@extends('admin.layouts.master')

@section('page-title')
    طلبات التعيين
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
                    <h5 class="page-title fs-21 mb-1">طلبات التعيين</h5>
                </div>
                @can('requisition-create')
                <a href="{{ route('admin.requisitions.create') }}" class="btn btn-primary btn-sm">إضافة طلب تعيين</a>
                @endcan
            </div>

            <div class="card">
                <div class="card-header align-items-center d-flex gap-3 flex-wrap">
                    <form action="{{ route('admin.requisitions.index') }}" method="GET" class="d-flex align-items-center gap-2 flex-wrap">
                        <input type="text" name="search" class="form-control" style="width: 180px" value="{{ request('search') }}" placeholder="كود أو مبرر">
                        <select name="status" class="form-select" style="width: 130px">
                            <option value="">كل الحالات</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>موافق عليه</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                        </select>
                        <select name="department_id" class="form-select" style="width: 180px">
                            <option value="">كل الأقسام</option>
                            @foreach ($departments as $d)
                                <option value="{{ $d->id }}" {{ request('department_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                            @endforeach
                        </select>
                        <select name="position_id" class="form-select" style="width: 180px">
                            <option value="">كل المناصب</option>
                            @foreach ($positions as $p)
                                <option value="{{ $p->id }}" {{ request('position_id') == $p->id ? 'selected' : '' }}>{{ $p->title }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-secondary">بحث</button>
                        <a href="{{ route('admin.requisitions.index') }}" class="btn btn-danger">مسح</a>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>كود الطلب</th>
                                    <th>القسم</th>
                                    <th>المنصب</th>
                                    <th>العدد</th>
                                    <th>الحالة</th>
                                    <th>تاريخ الطلب</th>
                                    <th>العمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($requisitions as $req)
                                    <tr>
                                        <td>{{ $loop->iteration + ($requisitions->currentPage() - 1) * $requisitions->perPage() }}</td>
                                        <td>{{ $req->requisition_code }}</td>
                                        <td>{{ $req->department->name ?? '-' }}</td>
                                        <td>{{ $req->position->title ?? '-' }}</td>
                                        <td>{{ $req->number_of_positions }}</td>
                                        <td>
                                            <span class="badge bg-{{ $req->status == 'approved' ? 'success' : ($req->status == 'rejected' ? 'danger' : 'warning') }}">
                                                {{ $req->status_name_ar }}
                                            </span>
                                        </td>
                                        <td>{{ $req->created_at->format('Y-m-d') }}</td>
                                        <td>
                                            <a href="{{ route('admin.requisitions.show', $req) }}" class="btn btn-sm btn-info">عرض</a>
                                            @if ($req->status === 'pending')
                                                @can('requisition-edit')
                                                <a href="{{ route('admin.requisitions.edit', $req) }}" class="btn btn-sm btn-primary">تعديل</a>
                                                @endcan
                                                @can('requisition-approve')
                                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $req->id }}">موافقة</button>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $req->id }}">رفض</button>
                                                @endcan
                                            @endif
                                            @if ($req->jobVacancy)
                                                <a href="{{ route('admin.job-vacancies.show', $req->job_vacancy_id) }}" class="btn btn-sm btn-secondary">الشاغر</a>
                                            @endif
                                        </td>
                                    </tr>

                                    @can('requisition-approve')
                                    @if ($req->status === 'pending')
                                    <div class="modal fade" id="approveModal{{ $req->id }}" tabindex="-1">
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
                                                    <form action="{{ route('admin.requisitions.approve', $req) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success">موافقة</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade" id="rejectModal{{ $req->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">رفض طلب التعيين</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('admin.requisitions.reject', $req) }}" method="POST">
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
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">لا توجد طلبات تعيين</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $requisitions->links() }}</div>
                </div>
            </div>
        </div>
    </div>
@stop
