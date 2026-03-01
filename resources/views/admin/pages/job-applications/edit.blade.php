@extends('admin.layouts.master')

@section('page-title')
    تعديل طلب توظيف
@stop

@section('css')
    <style>
        .form-floating label {
            right: auto;
            left: 0.75rem;
        }
    </style>
@stop

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li class="small">{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header d-flex justify-content-between align-items-center my-4">
                <h5 class="page-title mb-0">تعديل طلب توظيف</h5>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.job-applications.update', $application->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            name="status" id="status" required>
                                        <option value="pending" {{ old('status', $application->status) == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                        <option value="reviewing" {{ old('status', $application->status) == 'reviewing' ? 'selected' : '' }}>قيد المراجعة</option>
                                        <option value="shortlisted" {{ old('status', $application->status) == 'shortlisted' ? 'selected' : '' }}>قائمة مختصرة</option>
                                        <option value="interviewed" {{ old('status', $application->status) == 'interviewed' ? 'selected' : '' }}>تمت المقابلة</option>
                                        <option value="offered" {{ old('status', $application->status) == 'offered' ? 'selected' : '' }}>تم العرض</option>
                                        <option value="accepted" {{ old('status', $application->status) == 'accepted' ? 'selected' : '' }}>مقبول</option>
                                        <option value="rejected" {{ old('status', $application->status) == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                                    </select>
                                    <label>الحالة <span class="text-danger">*</span></label>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="number" class="form-control" 
                                           name="rating" placeholder="التقييم" 
                                           value="{{ old('rating', $application->rating) }}" min="1" max="5">
                                    <label>التقييم (1-5)</label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" name="reviewer_notes" placeholder="ملاحظات المراجع" style="height: 100px">{{ old('reviewer_notes', $application->reviewer_notes) }}</textarea>
                                    <label>ملاحظات المراجع</label>
                                </div>
                            </div>

                            <div class="col-12" id="rejection_reason_div" style="display: {{ old('status', $application->status) == 'rejected' ? 'block' : 'none' }};">
                                <div class="form-floating">
                                    <textarea class="form-control" name="rejection_reason" placeholder="سبب الرفض" style="height: 100px">{{ old('rejection_reason', $application->rejection_reason) }}</textarea>
                                    <label>سبب الرفض</label>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.job-applications.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>حفظ التعديلات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('status').addEventListener('change', function() {
            const rejectionDiv = document.getElementById('rejection_reason_div');
            if (this.value === 'rejected') {
                rejectionDiv.style.display = 'block';
            } else {
                rejectionDiv.style.display = 'none';
            }
        });
    </script>
@stop


