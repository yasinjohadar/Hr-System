@extends('admin.layouts.master')

@section('page-title')
    تعديل المهمة
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تعديل المهمة</h5>
                </div>
                <div>
                    <a href="{{ route('admin.tasks.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.tasks.update', $task->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">العنوان <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                       value="{{ old('title', $task->title) }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">العنوان (عربي)</label>
                                <input type="text" name="title_ar" class="form-control" value="{{ old('title_ar', $task->title_ar) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">رقم المهمة</label>
                                <input type="text" name="task_code" class="form-control @error('task_code') is-invalid @enderror" 
                                       value="{{ old('task_code', $task->task_code) }}">
                                @error('task_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">المشروع</label>
                                <select name="project_id" class="form-select">
                                    <option value="">اختر المشروع</option>
                                    @foreach ($projects as $proj)
                                        <option value="{{ $proj->id }}" {{ old('project_id', $task->project_id) == $proj->id ? 'selected' : '' }}>
                                            {{ $proj->name_ar ?? $proj->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">القسم</label>
                                <select name="department_id" class="form-select">
                                    <option value="">اختر القسم</option>
                                    @foreach ($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ old('department_id', $task->department_id) == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاريخ البدء</label>
                                <input type="date" name="start_date" class="form-control" 
                                       value="{{ old('start_date', $task->start_date?->format('Y-m-d')) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاريخ الاستحقاق</label>
                                <input type="date" name="due_date" class="form-control @error('due_date') is-invalid @enderror" 
                                       value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}">
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الحالة <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="pending" {{ old('status', $task->status) == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                    <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                                    <option value="in_review" {{ old('status', $task->status) == 'in_review' ? 'selected' : '' }}>قيد المراجعة</option>
                                    <option value="completed" {{ old('status', $task->status) == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                    <option value="cancelled" {{ old('status', $task->status) == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                                    <option value="on_hold" {{ old('status', $task->status) == 'on_hold' ? 'selected' : '' }}>معلق</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الأولوية <span class="text-danger">*</span></label>
                                <select name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                                    <option value="low" {{ old('priority', $task->priority) == 'low' ? 'selected' : '' }}>منخفض</option>
                                    <option value="medium" {{ old('priority', $task->priority) == 'medium' ? 'selected' : '' }}>متوسط</option>
                                    <option value="high" {{ old('priority', $task->priority) == 'high' ? 'selected' : '' }}>عالي</option>
                                    <option value="urgent" {{ old('priority', $task->priority) == 'urgent' ? 'selected' : '' }}>عاجل</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">نسبة الإنجاز (%)</label>
                                <input type="number" name="progress" class="form-control" min="0" max="100" value="{{ old('progress', $task->progress) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الساعات المتوقعة</label>
                                <input type="number" name="estimated_hours" class="form-control" min="0" value="{{ old('estimated_hours', $task->estimated_hours) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الساعات الفعلية</label>
                                <input type="number" name="actual_hours" class="form-control" min="0" value="{{ old('actual_hours', $task->actual_hours) }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label">الوصف</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description', $task->description) }}</textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label">الوصف (عربي)</label>
                                <textarea name="description_ar" class="form-control" rows="3">{{ old('description_ar', $task->description_ar) }}</textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label">ملاحظات</label>
                                <textarea name="notes" class="form-control" rows="3">{{ old('notes', $task->notes) }}</textarea>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.tasks.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>حفظ التغييرات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

