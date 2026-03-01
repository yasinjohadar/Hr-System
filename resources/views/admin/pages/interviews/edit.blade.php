@extends('admin.layouts.master')

@section('page-title')
    تعديل مقابلة
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
                <h5 class="page-title mb-0">تعديل مقابلة</h5>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.interviews.update', $interview->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="date" class="form-control @error('interview_date') is-invalid @enderror" 
                                           name="interview_date" placeholder="تاريخ المقابلة" 
                                           value="{{ old('interview_date', $interview->interview_date->format('Y-m-d')) }}" required>
                                    <label>تاريخ المقابلة <span class="text-danger">*</span></label>
                                    @error('interview_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="time" class="form-control @error('interview_time') is-invalid @enderror" 
                                           name="interview_time" placeholder="وقت المقابلة" 
                                           value="{{ old('interview_time', $interview->interview_time ? $interview->interview_time->format('H:i') : '09:00') }}" required>
                                    <label>وقت المقابلة <span class="text-danger">*</span></label>
                                    @error('interview_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            name="status" id="status" required>
                                        <option value="scheduled" {{ old('status', $interview->status) == 'scheduled' ? 'selected' : '' }}>مجدولة</option>
                                        <option value="confirmed" {{ old('status', $interview->status) == 'confirmed' ? 'selected' : '' }}>مؤكدة</option>
                                        <option value="in_progress" {{ old('status', $interview->status) == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                                        <option value="completed" {{ old('status', $interview->status) == 'completed' ? 'selected' : '' }}>مكتملة</option>
                                        <option value="cancelled" {{ old('status', $interview->status) == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                                    </select>
                                    <label>الحالة <span class="text-danger">*</span></label>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6" id="rejection_div" style="display: {{ old('status', $interview->status) == 'cancelled' ? 'block' : 'none' }};">
                                <div class="form-floating">
                                    <textarea class="form-control" name="cancellation_reason" placeholder="سبب الإلغاء" style="height: 100px">{{ old('cancellation_reason', $interview->cancellation_reason) }}</textarea>
                                    <label>سبب الإلغاء</label>
                                </div>
                            </div>

                            <div class="col-md-6" id="rating_div" style="display: {{ old('status', $interview->status) == 'completed' ? 'block' : 'none' }};">
                                <div class="form-floating">
                                    <input type="number" class="form-control" 
                                           name="overall_rating" placeholder="التقييم الإجمالي" 
                                           value="{{ old('overall_rating', $interview->overall_rating) }}" min="1" max="5">
                                    <label>التقييم الإجمالي (1-5)</label>
                                </div>
                            </div>

                            <div class="col-12" id="notes_div" style="display: {{ old('status', $interview->status) == 'completed' ? 'block' : 'none' }};">
                                <div class="form-floating">
                                    <textarea class="form-control" name="interview_notes" placeholder="ملاحظات المقابلة" style="height: 100px">{{ old('interview_notes', $interview->interview_notes) }}</textarea>
                                    <label>ملاحظات المقابلة</label>
                                </div>
                            </div>

                            <div class="col-md-6" id="recommendation_div" style="display: {{ old('status', $interview->status) == 'completed' ? 'block' : 'none' }};">
                                <div class="form-floating">
                                    <select class="form-select" name="recommendation_status" id="recommendation_status">
                                        <option value="">اختر التوصية</option>
                                        <option value="hire" {{ old('recommendation_status', $interview->recommendation_status) == 'hire' ? 'selected' : '' }}>توظيف</option>
                                        <option value="maybe" {{ old('recommendation_status', $interview->recommendation_status) == 'maybe' ? 'selected' : '' }}>قيد المراجعة</option>
                                        <option value="reject" {{ old('recommendation_status', $interview->recommendation_status) == 'reject' ? 'selected' : '' }}>رفض</option>
                                    </select>
                                    <label>التوصية</label>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.interviews.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
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
            const rejectionDiv = document.getElementById('rejection_div');
            const ratingDiv = document.getElementById('rating_div');
            const notesDiv = document.getElementById('notes_div');
            const recommendationDiv = document.getElementById('recommendation_div');
            
            if (this.value === 'cancelled') {
                rejectionDiv.style.display = 'block';
                ratingDiv.style.display = 'none';
                notesDiv.style.display = 'none';
                recommendationDiv.style.display = 'none';
            } else if (this.value === 'completed') {
                rejectionDiv.style.display = 'none';
                ratingDiv.style.display = 'block';
                notesDiv.style.display = 'block';
                recommendationDiv.style.display = 'block';
            } else {
                rejectionDiv.style.display = 'none';
                ratingDiv.style.display = 'none';
                notesDiv.style.display = 'none';
                recommendationDiv.style.display = 'none';
            }
        });
    </script>
@stop


