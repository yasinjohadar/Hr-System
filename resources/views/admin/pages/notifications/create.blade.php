@extends('admin.layouts.master')

@section('page-title')
    إرسال إشعار جديد
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
                <h5 class="page-title mb-0">إرسال إشعار جديد</h5>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.notifications.store') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           name="title" placeholder="عنوان الإشعار" value="{{ old('title') }}" required>
                                    <label>عنوان الإشعار <span class="text-danger">*</span></label>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('type') is-invalid @enderror" 
                                            name="type" id="type" required>
                                        <option value="system" {{ old('type', 'system') == 'system' ? 'selected' : '' }}>نظام</option>
                                        <option value="leave_request" {{ old('type') == 'leave_request' ? 'selected' : '' }}>طلب إجازة</option>
                                        <option value="attendance" {{ old('type') == 'attendance' ? 'selected' : '' }}>حضور</option>
                                        <option value="salary" {{ old('type') == 'salary' ? 'selected' : '' }}>راتب</option>
                                        <option value="performance_review" {{ old('type') == 'performance_review' ? 'selected' : '' }}>تقييم أداء</option>
                                        <option value="training" {{ old('type') == 'training' ? 'selected' : '' }}>تدريب</option>
                                        <option value="recruitment" {{ old('type') == 'recruitment' ? 'selected' : '' }}>توظيف</option>
                                        <option value="reminder" {{ old('type') == 'reminder' ? 'selected' : '' }}>تذكير</option>
                                    </select>
                                    <label>نوع الإشعار <span class="text-danger">*</span></label>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('recipient_type') is-invalid @enderror" 
                                            name="recipient_type" id="recipient_type" required>
                                        <option value="user" {{ old('recipient_type', 'user') == 'user' ? 'selected' : '' }}>مستخدم محدد</option>
                                        <option value="all" {{ old('recipient_type') == 'all' ? 'selected' : '' }}>جميع المستخدمين</option>
                                        <option value="role" {{ old('recipient_type') == 'role' ? 'selected' : '' }}>دور محدد</option>
                                    </select>
                                    <label>نوع المستلم <span class="text-danger">*</span></label>
                                    @error('recipient_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6" id="userSelectDiv" style="display: {{ old('recipient_type', 'user') == 'user' ? 'block' : 'none' }};">
                                <div class="form-floating">
                                    <select class="form-select" name="user_id" id="user_id">
                                        <option value="">اختر المستخدم</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <label>المستخدم</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select" name="color" id="color">
                                        <option value="info" {{ old('color', 'info') == 'info' ? 'selected' : '' }}>أزرق</option>
                                        <option value="success" {{ old('color') == 'success' ? 'selected' : '' }}>أخضر</option>
                                        <option value="warning" {{ old('color') == 'warning' ? 'selected' : '' }}>أصفر</option>
                                        <option value="danger" {{ old('color') == 'danger' ? 'selected' : '' }}>أحمر</option>
                                    </select>
                                    <label>اللون</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" 
                                           name="icon" placeholder="الأيقونة" value="{{ old('icon', 'fas fa-bell') }}">
                                    <label>الأيقونة (Font Awesome)</label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_important" id="is_important" 
                                           value="1" {{ old('is_important') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_important">
                                        إشعار مهم
                                    </label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control @error('message') is-invalid @enderror" 
                                              name="message" placeholder="رسالة الإشعار" style="height: 100px" required>{{ old('message') }}</textarea>
                                    <label>رسالة الإشعار <span class="text-danger">*</span></label>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" 
                                              name="message_ar" placeholder="رسالة الإشعار بالعربية" style="height: 100px">{{ old('message_ar') }}</textarea>
                                    <label>رسالة الإشعار بالعربية</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="url" class="form-control" 
                                           name="action_url" placeholder="رابط الإجراء" value="{{ old('action_url') }}">
                                    <label>رابط الإجراء (اختياري)</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" 
                                           name="action_text" placeholder="نص الإجراء" value="{{ old('action_text', 'عرض') }}">
                                    <label>نص الإجراء</label>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-paper-plane me-2"></i>إرسال الإشعار
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('recipient_type').addEventListener('change', function() {
            const userSelectDiv = document.getElementById('userSelectDiv');
            if (this.value === 'user') {
                userSelectDiv.style.display = 'block';
                document.getElementById('user_id').required = true;
            } else {
                userSelectDiv.style.display = 'none';
                document.getElementById('user_id').required = false;
            }
        });
    </script>
@stop


