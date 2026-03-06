@extends('admin.layouts.master')

@section('page-title')
    تعديل الإعلان
@stop

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header d-flex justify-content-between align-items-center my-4">
                <h5 class="page-title mb-0">تعديل الإعلان: {{ $announcement->title }}</h5>
                <a href="{{ route('admin.announcements.index') }}" class="btn btn-secondary">العودة للقائمة</a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.announcements.update', $announcement) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">العنوان <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                       name="title" value="{{ old('title', $announcement->title) }}" required maxlength="255">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">المحتوى</label>
                                <textarea class="form-control @error('content') is-invalid @enderror" name="content"
                                          rows="6">{{ old('content', $announcement->content) }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاريخ النشر</label>
                                <input type="date" class="form-control @error('publish_date') is-invalid @enderror"
                                       name="publish_date" value="{{ old('publish_date', $announcement->publish_date?->format('Y-m-d')) }}">
                                @error('publish_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاريخ الانتهاء</label>
                                <input type="date" class="form-control @error('expiry_date') is-invalid @enderror"
                                       name="expiry_date" value="{{ old('expiry_date', $announcement->expiry_date?->format('Y-m-d')) }}">
                                @error('expiry_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الحالة <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="draft" {{ old('status', $announcement->status) == 'draft' ? 'selected' : '' }}>مسودة</option>
                                    <option value="published" {{ old('status', $announcement->status) == 'published' ? 'selected' : '' }}>منشور</option>
                                    <option value="archived" {{ old('status', $announcement->status) == 'archived' ? 'selected' : '' }}>مؤرشف</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الاستهداف <span class="text-danger">*</span></label>
                                <select name="target_type" id="target_type" class="form-select @error('target_type') is-invalid @enderror" required>
                                    <option value="all" {{ old('target_type', $announcement->target_type) == 'all' ? 'selected' : '' }}>الجميع</option>
                                    <option value="department" {{ old('target_type', $announcement->target_type) == 'department' ? 'selected' : '' }}>قسم محدد</option>
                                    <option value="branch" {{ old('target_type', $announcement->target_type) == 'branch' ? 'selected' : '' }}>فرع محدد</option>
                                </select>
                                @error('target_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6" id="wrap_department" style="{{ old('target_type', $announcement->target_type) == 'department' ? '' : 'display:none' }}">
                                <label class="form-label">القسم</label>
                                <select name="department_id" class="form-select">
                                    <option value="">-- اختر القسم --</option>
                                    @foreach ($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ old('department_id', $announcement->department_id) == $dept->id ? 'selected' : '' }}>{{ $dept->name_ar ?? $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6" id="wrap_branch" style="{{ old('target_type', $announcement->target_type) == 'branch' ? '' : 'display:none' }}">
                                <label class="form-label">الفرع</label>
                                <select name="branch_id" class="form-select">
                                    <option value="">-- اختر الفرع --</option>
                                    @foreach ($branches as $br)
                                        <option value="{{ $br->id }}" {{ old('branch_id', $announcement->branch_id) == $br->id ? 'selected' : '' }}>{{ $br->name_ar ?? $br->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.announcements.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
                            <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-2"></i>حفظ التعديلات</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
document.getElementById('target_type').addEventListener('change', function() {
    var v = this.value;
    document.getElementById('wrap_department').style.display = v === 'department' ? 'block' : 'none';
    document.getElementById('wrap_branch').style.display = v === 'branch' ? 'block' : 'none';
});
</script>
@stop
