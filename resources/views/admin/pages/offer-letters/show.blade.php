@extends('admin.layouts.master')

@section('page-title')
    عرض تفاصيل عرض التعيين
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="page-header d-flex justify-content-between align-items-center my-4">
                <h5 class="page-title mb-0">عرض التعيين — {{ $offer_letter->jobApplication->candidate->full_name }}</h5>
                <div>
                    @if($offer_letter->status === \App\Models\OfferLetter::STATUS_DRAFT)
                        <form method="POST" action="{{ route('admin.offer-letters.send', $offer_letter) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm me-1">اعتبار مرسل</button>
                        </form>
                        <a href="{{ route('admin.offer-letters.edit', $offer_letter) }}" class="btn btn-warning btn-sm me-1">تعديل</a>
                    @endif
                    @if($offer_letter->status === \App\Models\OfferLetter::STATUS_SENT)
                        <form method="POST" action="{{ route('admin.offer-letters.accept', $offer_letter) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm me-1">قبول العرض</button>
                        </form>
                        <button type="button" class="btn btn-danger btn-sm me-1" data-bs-toggle="modal" data-bs-target="#rejectModal">رفض العرض</button>
                    @endif
                    <a href="{{ route('admin.offer-letters.index') }}" class="btn btn-secondary btn-sm">العودة للقائمة</a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">تفاصيل عرض التعيين</h4>
                    <div class="mt-2">
                        <span class="badge {{ $offer_letter->status === 'draft' ? 'bg-secondary' : ($offer_letter->status === 'sent' ? 'bg-info' : ($offer_letter->status === 'accepted' ? 'bg-success' : 'bg-danger')) }}">
                            {{ $offer_letter->status_name_ar }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">المرشح</label>
                            <p class="form-control-plaintext">
                                <a href="{{ route('admin.candidates.show', $offer_letter->jobApplication->candidate_id) }}">{{ $offer_letter->jobApplication->candidate->full_name }}</a>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">الوظيفة الشاغرة</label>
                            <p class="form-control-plaintext">
                                <a href="{{ route('admin.job-vacancies.show', $offer_letter->jobApplication->job_vacancy_id) }}">{{ $offer_letter->jobApplication->jobVacancy->title_ar ?? $offer_letter->jobApplication->jobVacancy->title }}</a>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">المسمى الوظيفي في العرض</label>
                            <p class="form-control-plaintext">{{ $offer_letter->job_title }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">الراتب</label>
                            <p class="form-control-plaintext">
                                @if($offer_letter->salary !== null)
                                    {{ number_format($offer_letter->salary, 2) }}
                                    @if($offer_letter->currency) {{ $offer_letter->currency->code }} @endif
                                @else
                                    —
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">تاريخ البدء</label>
                            <p class="form-control-plaintext">{{ $offer_letter->start_date ? $offer_letter->start_date->format('Y-m-d') : '—' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">صلاحية العرض حتى</label>
                            <p class="form-control-plaintext">{{ $offer_letter->valid_until ? $offer_letter->valid_until->format('Y-m-d') : '—' }}</p>
                        </div>
                        @if($offer_letter->sent_at)
                            <div class="col-md-6">
                                <label class="form-label fw-bold">تاريخ الإرسال</label>
                                <p class="form-control-plaintext">{{ $offer_letter->sent_at->format('Y-m-d H:i') }}</p>
                            </div>
                        @endif
                        @if($offer_letter->accepted_at)
                            <div class="col-md-6">
                                <label class="form-label fw-bold">تاريخ القبول</label>
                                <p class="form-control-plaintext">{{ $offer_letter->accepted_at->format('Y-m-d H:i') }}</p>
                            </div>
                        @endif
                        @if($offer_letter->rejected_at)
                            <div class="col-md-6">
                                <label class="form-label fw-bold">تاريخ الرفض</label>
                                <p class="form-control-plaintext">{{ $offer_letter->rejected_at->format('Y-m-d H:i') }}</p>
                            </div>
                            @if($offer_letter->rejection_reason)
                                <div class="col-12">
                                    <label class="form-label fw-bold">سبب الرفض</label>
                                    <p class="form-control-plaintext">{{ $offer_letter->rejection_reason }}</p>
                                </div>
                            @endif
                        @endif
                        @if($offer_letter->creator)
                            <div class="col-md-6">
                                <label class="form-label fw-bold">أنشأه</label>
                                <p class="form-control-plaintext">{{ $offer_letter->creator->name }}</p>
                            </div>
                        @endif
                    </div>
                    @if($offer_letter->notes)
                        <div class="mb-3">
                            <label class="form-label fw-bold">ملاحظات</label>
                            <p class="form-control-plaintext">{{ $offer_letter->notes }}</p>
                        </div>
                    @endif
                    @if($offer_letter->document_path)
                        <div>
                            <label class="form-label fw-bold">مرفق المستند</label>
                            <p class="form-control-plaintext">
                                <a href="{{ asset('storage/' . $offer_letter->document_path) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="fa-solid fa-file-pdf me-1"></i>عرض/تحميل المستند
                                </a>
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Modal رفض العرض --}}
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.offer-letters.reject', $offer_letter) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">رفض عرض التعيين</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">سبب الرفض (اختياري)</label>
                            <textarea name="rejection_reason" class="form-control" rows="3" maxlength="1000" placeholder="يمكنك توضيح سبب الرفض...">{{ old('rejection_reason') }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-danger">تسجيل الرفض</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
@stop
