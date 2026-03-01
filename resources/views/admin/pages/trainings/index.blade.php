@extends('admin.layouts.master')

@section('page-title')
    قائمة الدورات التدريبية
@stop

@section('css')
@stop

@section('content')
    @if (\Session::has('success'))
        <div class="alert alert-success">
            <ul>
                <li>{!! \Session::get('success') !!}</li>
            </ul>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
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
                    <h5 class="page-title fs-21 mb-1">كافة الدورات التدريبية</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex gap-3">
                            @can('training-create')
                            <a href="{{ route('admin.trainings.create') }}" class="btn btn-primary btn-sm">إضافة دورة تدريبية جديدة</a>
                            @endcan

                            <div class="flex-shrink-0">
                                <form action="{{ route('admin.trainings.index') }}" method="GET" class="d-flex align-items-center gap-2">
                                    <input type="text" name="search" class="form-control" style="width: 200px;"
                                           placeholder="بحث..." value="{{ request('search') }}">
                                    <select name="type" class="form-select" style="width: 150px;">
                                        <option value="">كل الأنواع</option>
                                        <option value="internal" {{ request('type') == 'internal' ? 'selected' : '' }}>داخلي</option>
                                        <option value="external" {{ request('type') == 'external' ? 'selected' : '' }}>خارجي</option>
                                        <option value="online" {{ request('type') == 'online' ? 'selected' : '' }}>أونلاين</option>
                                        <option value="workshop" {{ request('type') == 'workshop' ? 'selected' : '' }}>ورشة عمل</option>
                                    </select>
                                    <select name="status" class="form-select" style="width: 150px;">
                                        <option value="">كل الحالات</option>
                                        <option value="planned" {{ request('status') == 'planned' ? 'selected' : '' }}>مخطط</option>
                                        <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>قيد التنفيذ</option>
                                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                                    </select>
                                    <select name="instructor_id" class="form-select" style="width: 200px;">
                                        <option value="">كل المدربين</option>
                                        @foreach ($instructors as $instructor)
                                            <option value="{{ $instructor->id }}" {{ request('instructor_id') == $instructor->id ? 'selected' : '' }}>
                                                {{ $instructor->full_name ?? $instructor->first_name . ' ' . $instructor->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-secondary">بحث</button>
                                    <a href="{{ route('admin.trainings.index') }}" class="btn btn-danger">مسح</a>
                                </form>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle table-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>عنوان الدورة</th>
                                            <th>الكود</th>
                                            <th>النوع</th>
                                            <th>المدرب</th>
                                            <th>تاريخ البدء</th>
                                            <th>المشاركون</th>
                                            <th>الحالة</th>
                                            <th>العمليات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($trainings as $training)
                                            <tr>
                                                <th>{{ $loop->iteration }}</th>
                                                <td>
                                                    <strong>{{ $training->title_ar ?? $training->title }}</strong>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ $training->code }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $training->type_ar }}</span>
                                                </td>
                                                <td>
                                                    @if ($training->instructor)
                                                        {{ $training->instructor->full_name ?? $training->instructor->first_name . ' ' . $training->instructor->last_name }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>{{ $training->start_date->format('Y-m-d') }}</td>
                                                <td>
                                                    <span class="badge bg-primary">
                                                        {{ $training->participants_count }}
                                                        @if ($training->max_participants)
                                                            / {{ $training->max_participants }}
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>
                                                    @if ($training->status == 'completed')
                                                        <span class="badge bg-success">مكتمل</span>
                                                    @elseif ($training->status == 'ongoing')
                                                        <span class="badge bg-primary">قيد التنفيذ</span>
                                                    @elseif ($training->status == 'planned')
                                                        <span class="badge bg-info">مخطط</span>
                                                    @else
                                                        <span class="badge bg-danger">ملغي</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @can('training-show')
                                                    <a class="btn btn-info btn-sm me-1" href="{{ route('admin.trainings.show', $training->id) }}" title="عرض">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                    @endcan
                                                    @can('training-edit')
                                                    <a class="btn btn-warning btn-sm me-1" href="{{ route('admin.trainings.edit', $training->id) }}" title="تعديل">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </a>
                                                    @endcan
                                                    @can('training-delete')
                                                    <a class="btn btn-danger btn-sm me-1" data-bs-toggle="modal" data-bs-target="#delete{{ $training->id }}" title="حذف">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </a>
                                                    @endcan
                                                </td>
                                            </tr>
                                            @include('admin.pages.trainings.delete')
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center text-danger fw-bold">لا توجد بيانات متاحة</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <div class="mt-3">
                                    {{ $trainings->withQueryString()->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
@stop


