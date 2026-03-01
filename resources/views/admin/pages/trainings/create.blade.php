@extends('admin.layouts.master')

@section('page-title')
    إضافة دورة تدريبية جديدة
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
                <h5 class="page-title mb-0">إضافة دورة تدريبية جديدة</h5>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.trainings.store') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           name="title" id="title" placeholder="عنوان الدورة" 
                                           value="{{ old('title') }}" required>
                                    <label>عنوان الدورة (إنجليزي) <span class="text-danger">*</span></label>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('title_ar') is-invalid @enderror" 
                                           name="title_ar" id="title_ar" placeholder="عنوان الدورة بالعربية" 
                                           value="{{ old('title_ar') }}">
                                    <label>عنوان الدورة (عربي)</label>
                                    @error('title_ar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                           name="code" id="code" placeholder="كود الدورة" 
                                           value="{{ old('code') }}" required>
                                    <label>كود الدورة <span class="text-danger">*</span></label>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select @error('type') is-invalid @enderror" 
                                            name="type" id="type" required>
                                        <option value="internal" {{ old('type') == 'internal' ? 'selected' : '' }}>داخلي</option>
                                        <option value="external" {{ old('type') == 'external' ? 'selected' : '' }}>خارجي</option>
                                        <option value="online" {{ old('type') == 'online' ? 'selected' : '' }}>أونلاين</option>
                                        <option value="workshop" {{ old('type') == 'workshop' ? 'selected' : '' }}>ورشة عمل</option>
                                    </select>
                                    <label>نوع التدريب <span class="text-danger">*</span></label>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            name="status" id="status" required>
                                        <option value="planned" {{ old('status', 'planned') == 'planned' ? 'selected' : '' }}>مخطط</option>
                                        <option value="ongoing" {{ old('status') == 'ongoing' ? 'selected' : '' }}>قيد التنفيذ</option>
                                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                                    </select>
                                    <label>الحالة <span class="text-danger">*</span></label>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                           name="start_date" id="start_date" placeholder="تاريخ البدء" 
                                           value="{{ old('start_date') }}" required>
                                    <label>تاريخ البدء <span class="text-danger">*</span></label>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                           name="end_date" id="end_date" placeholder="تاريخ الانتهاء" 
                                           value="{{ old('end_date') }}" required>
                                    <label>تاريخ الانتهاء <span class="text-danger">*</span></label>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="time" class="form-control" 
                                           name="start_time" id="start_time" placeholder="وقت البدء" 
                                           value="{{ old('start_time') }}">
                                    <label>وقت البدء</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="time" class="form-control" 
                                           name="end_time" id="end_time" placeholder="وقت الانتهاء" 
                                           value="{{ old('end_time') }}">
                                    <label>وقت الانتهاء</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="number" class="form-control" 
                                           name="duration_hours" id="duration_hours" placeholder="المدة بالساعات" 
                                           value="{{ old('duration_hours', 0) }}" min="0">
                                    <label>المدة بالساعات</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" 
                                           name="provider" id="provider" placeholder="مقدم التدريب" 
                                           value="{{ old('provider') }}">
                                    <label>مقدم التدريب</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" 
                                           name="location" id="location" placeholder="مكان التدريب" 
                                           value="{{ old('location') }}">
                                    <label>مكان التدريب</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="number" class="form-control" 
                                           name="max_participants" id="max_participants" placeholder="الحد الأقصى للمشاركين" 
                                           value="{{ old('max_participants') }}" min="1">
                                    <label>الحد الأقصى للمشاركين</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="number" class="form-control" 
                                           name="cost" id="cost" placeholder="التكلفة" 
                                           value="{{ old('cost', 0) }}" min="0" step="0.01">
                                    <label>التكلفة</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select" name="currency_id" id="currency_id">
                                        <option value="">اختر العملة</option>
                                        @foreach ($currencies as $currency)
                                            <option value="{{ $currency->id }}" {{ old('currency_id') == $currency->id ? 'selected' : '' }}>
                                                {{ $currency->name_ar ?? $currency->name }} ({{ $currency->symbol_ar ?? $currency->symbol }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <label>العملة</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" name="instructor_id" id="instructor_id">
                                        <option value="">اختر المدرب</option>
                                        @foreach ($instructors as $instructor)
                                            <option value="{{ $instructor->id }}" {{ old('instructor_id') == $instructor->id ? 'selected' : '' }}>
                                                {{ $instructor->full_name ?? $instructor->first_name . ' ' . $instructor->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label>المدرب</label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" name="description" placeholder="الوصف" style="height: 100px">{{ old('description') }}</textarea>
                                    <label>الوصف (إنجليزي)</label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" name="description_ar" placeholder="الوصف بالعربية" style="height: 100px">{{ old('description_ar') }}</textarea>
                                    <label>الوصف (عربي)</label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" name="objectives" placeholder="الأهداف" style="height: 100px">{{ old('objectives') }}</textarea>
                                    <label>الأهداف</label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" name="content" placeholder="المحتوى" style="height: 100px">{{ old('content') }}</textarea>
                                    <label>المحتوى</label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" name="materials" placeholder="المواد التدريبية" style="height: 100px">{{ old('materials') }}</textarea>
                                    <label>المواد التدريبية</label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" name="notes" placeholder="ملاحظات" style="height: 100px">{{ old('notes') }}</textarea>
                                    <label>ملاحظات</label>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.trainings.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>حفظ الدورة
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop


