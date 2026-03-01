@extends('admin.layouts.master')

@section('page-title')
    إضافة تقييم جديد
@stop

@section('css')
    <style>
        .form-floating label {
            right: auto;
            left: 0.75rem;
        }
        .rating-stars {
            font-size: 24px;
            cursor: pointer;
        }
        .rating-stars .star {
            color: #ddd;
            transition: color 0.2s;
        }
        .rating-stars .star.active {
            color: #ffc107;
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
                <h5 class="page-title mb-0">إضافة تقييم جديد</h5>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.performance-reviews.store') }}" id="reviewForm">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('employee_id') is-invalid @enderror" 
                                            name="employee_id" id="employee_id" required>
                                        <option value="">اختر الموظف</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->full_name ?? $employee->first_name . ' ' . $employee->last_name }} 
                                                ({{ $employee->employee_code ?? '' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <label>الموظف المقيّم <span class="text-danger">*</span></label>
                                    @error('employee_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('reviewer_id') is-invalid @enderror" 
                                            name="reviewer_id" id="reviewer_id" required>
                                        <option value="">اختر المقيّم</option>
                                        @foreach ($reviewers as $reviewer)
                                            <option value="{{ $reviewer->id }}" {{ old('reviewer_id') == $reviewer->id ? 'selected' : '' }}>
                                                {{ $reviewer->full_name ?? $reviewer->first_name . ' ' . $reviewer->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label>المقيّم (المدير) <span class="text-danger">*</span></label>
                                    @error('reviewer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('review_period') is-invalid @enderror" 
                                           name="review_period" id="review_period" placeholder="فترة التقييم" 
                                           value="{{ old('review_period') }}" required>
                                    <label>فترة التقييم <span class="text-danger">*</span></label>
                                    <small class="form-text text-muted">مثال: Q1 2024, Annual 2024</small>
                                    @error('review_period')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="date" class="form-control @error('review_date') is-invalid @enderror" 
                                           name="review_date" id="review_date" placeholder="تاريخ التقييم" 
                                           value="{{ old('review_date', date('Y-m-d')) }}" required>
                                    <label>تاريخ التقييم <span class="text-danger">*</span></label>
                                    @error('review_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-floating">
                                    <input type="date" class="form-control @error('period_start_date') is-invalid @enderror" 
                                           name="period_start_date" id="period_start_date" placeholder="من تاريخ" 
                                           value="{{ old('period_start_date') }}" required>
                                    <label>من تاريخ <span class="text-danger">*</span></label>
                                    @error('period_start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-floating">
                                    <input type="date" class="form-control @error('period_end_date') is-invalid @enderror" 
                                           name="period_end_date" id="period_end_date" placeholder="إلى تاريخ" 
                                           value="{{ old('period_end_date') }}" required>
                                    <label>إلى تاريخ <span class="text-danger">*</span></label>
                                    @error('period_end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <hr>
                                <h6 class="mb-3">التقييمات (من 1 إلى 5)</h6>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">المعرفة الوظيفية</label>
                                <select class="form-select @error('job_knowledge') is-invalid @enderror" 
                                        name="job_knowledge" id="job_knowledge">
                                    <option value="0">اختر التقييم</option>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}" {{ old('job_knowledge') == $i ? 'selected' : '' }}>
                                            {{ $i }} - {{ ['', 'ضعيف جداً', 'ضعيف', 'مقبول', 'جيد', 'ممتاز'][$i] }}
                                        </option>
                                    @endfor
                                </select>
                                @error('job_knowledge')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">جودة العمل</label>
                                <select class="form-select @error('work_quality') is-invalid @enderror" 
                                        name="work_quality" id="work_quality">
                                    <option value="0">اختر التقييم</option>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}" {{ old('work_quality') == $i ? 'selected' : '' }}>
                                            {{ $i }} - {{ ['', 'ضعيف جداً', 'ضعيف', 'مقبول', 'جيد', 'ممتاز'][$i] }}
                                        </option>
                                    @endfor
                                </select>
                                @error('work_quality')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الإنتاجية</label>
                                <select class="form-select @error('productivity') is-invalid @enderror" 
                                        name="productivity" id="productivity">
                                    <option value="0">اختر التقييم</option>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}" {{ old('productivity') == $i ? 'selected' : '' }}>
                                            {{ $i }} - {{ ['', 'ضعيف جداً', 'ضعيف', 'مقبول', 'جيد', 'ممتاز'][$i] }}
                                        </option>
                                    @endfor
                                </select>
                                @error('productivity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">التواصل</label>
                                <select class="form-select @error('communication') is-invalid @enderror" 
                                        name="communication" id="communication">
                                    <option value="0">اختر التقييم</option>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}" {{ old('communication') == $i ? 'selected' : '' }}>
                                            {{ $i }} - {{ ['', 'ضعيف جداً', 'ضعيف', 'مقبول', 'جيد', 'ممتاز'][$i] }}
                                        </option>
                                    @endfor
                                </select>
                                @error('communication')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">العمل الجماعي</label>
                                <select class="form-select @error('teamwork') is-invalid @enderror" 
                                        name="teamwork" id="teamwork">
                                    <option value="0">اختر التقييم</option>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}" {{ old('teamwork') == $i ? 'selected' : '' }}>
                                            {{ $i }} - {{ ['', 'ضعيف جداً', 'ضعيف', 'مقبول', 'جيد', 'ممتاز'][$i] }}
                                        </option>
                                    @endfor
                                </select>
                                @error('teamwork')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">المبادرة</label>
                                <select class="form-select @error('initiative') is-invalid @enderror" 
                                        name="initiative" id="initiative">
                                    <option value="0">اختر التقييم</option>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}" {{ old('initiative') == $i ? 'selected' : '' }}>
                                            {{ $i }} - {{ ['', 'ضعيف جداً', 'ضعيف', 'مقبول', 'جيد', 'ممتاز'][$i] }}
                                        </option>
                                    @endfor
                                </select>
                                @error('initiative')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">حل المشاكل</label>
                                <select class="form-select @error('problem_solving') is-invalid @enderror" 
                                        name="problem_solving" id="problem_solving">
                                    <option value="0">اختر التقييم</option>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}" {{ old('problem_solving') == $i ? 'selected' : '' }}>
                                            {{ $i }} - {{ ['', 'ضعيف جداً', 'ضعيف', 'مقبول', 'جيد', 'ممتاز'][$i] }}
                                        </option>
                                    @endfor
                                </select>
                                @error('problem_solving')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الحضور والانضباط</label>
                                <select class="form-select @error('attendance_punctuality') is-invalid @enderror" 
                                        name="attendance_punctuality" id="attendance_punctuality">
                                    <option value="0">اختر التقييم</option>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}" {{ old('attendance_punctuality') == $i ? 'selected' : '' }}>
                                            {{ $i }} - {{ ['', 'ضعيف جداً', 'ضعيف', 'مقبول', 'جيد', 'ممتاز'][$i] }}
                                        </option>
                                    @endfor
                                </select>
                                @error('attendance_punctuality')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <div class="form-floating">
                                    <input type="text" class="form-control" 
                                           id="overall_rating_display" placeholder="التقييم الإجمالي" readonly>
                                    <label>التقييم الإجمالي</label>
                                    <small class="form-text text-muted">يتم الحساب تلقائياً</small>
                                </div>
                            </div>

                            <div class="col-12">
                                <hr>
                                <h6 class="mb-3">التعليقات والملاحظات</h6>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <textarea class="form-control @error('strengths') is-invalid @enderror" 
                                              name="strengths" placeholder="نقاط القوة" style="height: 100px">{{ old('strengths') }}</textarea>
                                    <label>نقاط القوة</label>
                                    @error('strengths')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <textarea class="form-control @error('weaknesses') is-invalid @enderror" 
                                              name="weaknesses" placeholder="نقاط الضعف" style="height: 100px">{{ old('weaknesses') }}</textarea>
                                    <label>نقاط الضعف</label>
                                    @error('weaknesses')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <textarea class="form-control @error('goals_achieved') is-invalid @enderror" 
                                              name="goals_achieved" placeholder="الأهداف المحققة" style="height: 100px">{{ old('goals_achieved') }}</textarea>
                                    <label>الأهداف المحققة</label>
                                    @error('goals_achieved')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <textarea class="form-control @error('future_goals') is-invalid @enderror" 
                                              name="future_goals" placeholder="الأهداف المستقبلية" style="height: 100px">{{ old('future_goals') }}</textarea>
                                    <label>الأهداف المستقبلية</label>
                                    @error('future_goals')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control @error('comments') is-invalid @enderror" 
                                              name="comments" placeholder="تعليقات المقيّم" style="height: 100px">{{ old('comments') }}</textarea>
                                    <label>تعليقات المقيّم</label>
                                    @error('comments')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            name="status" id="status" required>
                                        <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>مسودة</option>
                                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                        <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>موافق عليه</option>
                                        <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                                    </select>
                                    <label>الحالة <span class="text-danger">*</span></label>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.performance-reviews.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>حفظ التقييم
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
<script>
    // حساب التقييم الإجمالي تلقائياً
    function calculateOverallRating() {
        const ratings = [
            parseInt(document.getElementById('job_knowledge').value) || 0,
            parseInt(document.getElementById('work_quality').value) || 0,
            parseInt(document.getElementById('productivity').value) || 0,
            parseInt(document.getElementById('communication').value) || 0,
            parseInt(document.getElementById('teamwork').value) || 0,
            parseInt(document.getElementById('initiative').value) || 0,
            parseInt(document.getElementById('problem_solving').value) || 0,
            parseInt(document.getElementById('attendance_punctuality').value) || 0,
        ];

        const validRatings = ratings.filter(r => r > 0);
        const average = validRatings.length > 0 
            ? (validRatings.reduce((a, b) => a + b, 0) / validRatings.length).toFixed(2)
            : '0.00';
        
        document.getElementById('overall_rating_display').value = average + ' / 5.00';
    }

    // إضافة مستمعين للأحداث
    ['job_knowledge', 'work_quality', 'productivity', 'communication', 'teamwork', 'initiative', 'problem_solving', 'attendance_punctuality'].forEach(id => {
        document.getElementById(id).addEventListener('change', calculateOverallRating);
    });

    calculateOverallRating();
</script>
@stop


