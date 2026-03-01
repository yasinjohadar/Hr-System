@extends('admin.layouts.master')

@section('page-title')
    قائمة المرشحين
@stop

@section('content')
    @if (\Session::has('success'))
        <div class="alert alert-success">
            <ul>
                <li>{!! \Session::get('success') !!}</li>
            </ul>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">كافة المرشحين</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex gap-3">
                            @can('candidate-create')
                            <a href="{{ route('admin.candidates.create') }}" class="btn btn-primary btn-sm">إضافة مرشح جديد</a>
                            @endcan

                            <div class="flex-shrink-0">
                                <form action="{{ route('admin.candidates.index') }}" method="GET" class="d-flex align-items-center gap-2">
                                    <input style="width: 300px" type="text" name="search" class="form-control"
                                        placeholder="بحث بالاسم أو البريد أو الهاتف" value="{{ request('search') }}">
                                    <select name="status" class="form-select">
                                        <option value="">كل الحالات</option>
                                        <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>جديد</option>
                                        <option value="contacted" {{ request('status') == 'contacted' ? 'selected' : '' }}>تم التواصل</option>
                                        <option value="interviewed" {{ request('status') == 'interviewed' ? 'selected' : '' }}>تمت المقابلة</option>
                                        <option value="hired" {{ request('status') == 'hired' ? 'selected' : '' }}>تم التوظيف</option>
                                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                                    </select>
                                    <button type="submit" class="btn btn-secondary">بحث</button>
                                    <a href="{{ route('admin.candidates.index') }}" class="btn btn-danger">مسح</a>
                                </form>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle table-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>الاسم</th>
                                            <th>البريد الإلكتروني</th>
                                            <th>الهاتف</th>
                                            <th>المنصب الحالي</th>
                                            <th>سنوات الخبرة</th>
                                            <th>الحالة</th>
                                            <th>العمليات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($candidates as $candidate)
                                            <tr>
                                                <th>{{ $loop->iteration }}</th>
                                                <td>
                                                    <strong>{{ $candidate->full_name }}</strong>
                                                    <br><small class="text-muted">{{ $candidate->candidate_code }}</small>
                                                </td>
                                                <td>{{ $candidate->email }}</td>
                                                <td>{{ $candidate->phone }}</td>
                                                <td>
                                                    @if ($candidate->current_position)
                                                        {{ $candidate->current_position }}
                                                        @if ($candidate->current_company)
                                                            <br><small class="text-muted">في {{ $candidate->current_company }}</small>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($candidate->years_of_experience)
                                                        <span class="badge bg-info">{{ $candidate->years_of_experience }} سنة</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $candidate->status == 'hired' ? 'success' : ($candidate->status == 'rejected' ? 'danger' : ($candidate->status == 'interviewed' ? 'primary' : 'warning')) }}">
                                                        {{ $candidate->status_name_ar }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @can('candidate-show')
                                                    <a class="btn btn-info btn-sm me-1" href="{{ route('admin.candidates.show', $candidate->id) }}" title="عرض">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                    @endcan
                                                    @can('candidate-edit')
                                                    <a class="btn btn-warning btn-sm me-1" href="{{ route('admin.candidates.edit', $candidate->id) }}" title="تعديل">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </a>
                                                    @endcan
                                                    @can('candidate-delete')
                                                    <a class="btn btn-danger btn-sm me-1" data-bs-toggle="modal" data-bs-target="#delete{{ $candidate->id }}" title="حذف">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </a>
                                                    @endcan
                                                </td>
                                            </tr>
                                            @include('admin.pages.candidates.delete')
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-danger fw-bold">لا توجد بيانات متاحة</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <div class="mt-3">
                                    {{ $candidates->withQueryString()->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop


