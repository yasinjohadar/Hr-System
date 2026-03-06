@extends('admin.layouts.master')

@section('page-title')
    عروض التعيين
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
                    <h5 class="page-title fs-21 mb-1">عروض التعيين</h5>
                </div>
                @can('offer-letter-create')
                <a href="{{ route('admin.offer-letters.create') }}" class="btn btn-primary btn-sm">إنشاء عرض تعيين</a>
                @endcan
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex gap-3 flex-wrap">
                            <form action="{{ route('admin.offer-letters.index') }}" method="GET" class="d-flex align-items-center gap-2 flex-wrap">
                                <input type="text" name="search" class="form-control" style="width: 200px" placeholder="بحث بالمرشح..." value="{{ request('search') }}">
                                <select name="status" class="form-select" style="width: 140px">
                                    <option value="">كل الحالات</option>
                                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>مرسل</option>
                                    <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>مقبول</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                                </select>
                                <select name="job_vacancy_id" class="form-select" style="width: 200px">
                                    <option value="">كل الوظائف</option>
                                    @foreach ($vacancies as $v)
                                        <option value="{{ $v->id }}" {{ request('job_vacancy_id') == $v->id ? 'selected' : '' }}>{{ $v->title_ar ?? $v->title }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-secondary">بحث</button>
                                <a href="{{ route('admin.offer-letters.index') }}" class="btn btn-outline-secondary">مسح</a>
                            </form>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle table-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>المرشح</th>
                                            <th>الوظيفة</th>
                                            <th>المسمى في العرض</th>
                                            <th>الراتب</th>
                                            <th>تاريخ البدء</th>
                                            <th>الحالة</th>
                                            <th>تاريخ الإرسال</th>
                                            <th>العمليات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($offers as $offer)
                                            <tr>
                                                <td>{{ $loop->iteration + ($offers->currentPage() - 1) * $offers->perPage() }}</td>
                                                <td>
                                                    <strong>{{ $offer->jobApplication->candidate->full_name ?? '-' }}</strong>
                                                    <br><small class="text-muted">{{ $offer->jobApplication->candidate->email ?? '' }}</small>
                                                </td>
                                                <td>{{ $offer->jobApplication->jobVacancy->title ?? ($offer->jobApplication->jobVacancy->title_ar ?? '-') }}</td>
                                                <td>{{ $offer->job_title }}</td>
                                                <td>
                                                    @if($offer->salary)
                                                        {{ number_format($offer->salary, 2) }} {{ $offer->currency->code ?? '' }}
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td>{{ $offer->start_date ? $offer->start_date->format('Y-m-d') : '—' }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $offer->status == 'accepted' ? 'success' : ($offer->status == 'rejected' ? 'danger' : ($offer->status == 'sent' ? 'info' : 'secondary')) }}">
                                                        {{ $offer->status_name_ar }}
                                                    </span>
                                                </td>
                                                <td>{{ $offer->sent_at ? $offer->sent_at->format('Y-m-d H:i') : '—' }}</td>
                                                <td>
                                                    @can('offer-letter-show')
                                                    <a class="btn btn-info btn-sm me-1" href="{{ route('admin.offer-letters.show', $offer) }}" title="عرض"><i class="fa-solid fa-eye"></i></a>
                                                    @endcan
                                                    @can('offer-letter-edit')
                                                    @if($offer->status == 'draft')
                                                    <a class="btn btn-warning btn-sm me-1" href="{{ route('admin.offer-letters.edit', $offer) }}" title="تعديل"><i class="fa-solid fa-pen-to-square"></i></a>
                                                    @endif
                                                    @endcan
                                                    @can('offer-letter-delete')
                                                    @if($offer->status == 'draft')
                                                    <form action="{{ route('admin.offer-letters.destroy', $offer) }}" method="POST" class="d-inline" onsubmit="return confirm('حذف عرض التعيين؟');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" title="حذف"><i class="fa-solid fa-trash-can"></i></button>
                                                    </form>
                                                    @endif
                                                    @endcan
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center text-muted py-4">لا توجد عروض تعيين.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @if ($offers->hasPages())
                                <div class="mt-3">{{ $offers->withQueryString()->links() }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
