@extends('admin.layouts.master')

@section('page-title')
    تعديل الإعلان
@stop

@section('content')
    {{-- نفس بنية create.blade.php — أي تعديل على أحدهما يُنقل للآخر. --}}
    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">
            @include('admin.pages.users.partials.alerts')

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="ri-edit-box-line"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>تعديل الإعلان</h1>
                        <p>{{ $announcement->title }}</p>
                    </div>
                </div>
                <div class="admin-page-banner-actions">
                    <a href="{{ route('admin.announcements.index') }}" class="admin-btn admin-btn-light">
                        <i class="ri-arrow-right-line"></i>
                        العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="admin-page-card">
                <form method="POST" action="{{ route('admin.announcements.update', $announcement) }}" class="admin-form">
                    @csrf
                    @method('PUT')

                    <div class="admin-form-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="admin-form-label" for="title">العنوان <span class="text-danger">*</span></label>
                                <input type="text" id="title" name="title" maxlength="255" required
                                       class="form-control @error('title') is-invalid @enderror"
                                       value="{{ old('title', $announcement->title) }}">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="admin-form-label" for="content">المحتوى</label>
                                <textarea id="content" name="content" rows="6" placeholder="نص الإعلان"
                                          class="form-control @error('content') is-invalid @enderror">{{ old('content', $announcement->content) }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="admin-form-label" for="publish_date">تاريخ النشر</label>
                                <input type="date" id="publish_date" name="publish_date"
                                       class="form-control @error('publish_date') is-invalid @enderror"
                                       value="{{ old('publish_date', $announcement->publish_date?->format('Y-m-d')) }}">
                                @error('publish_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="admin-form-label" for="expiry_date">تاريخ الانتهاء</label>
                                <input type="date" id="expiry_date" name="expiry_date"
                                       class="form-control @error('expiry_date') is-invalid @enderror"
                                       value="{{ old('expiry_date', $announcement->expiry_date?->format('Y-m-d')) }}">
                                @error('expiry_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="admin-form-label" for="status">الحالة <span class="text-danger">*</span></label>
                                <select id="status" name="status" required
                                        class="form-select @error('status') is-invalid @enderror">
                                    <option value="draft" @selected(old('status', $announcement->status) === 'draft')>مسودة</option>
                                    <option value="published" @selected(old('status', $announcement->status) === 'published')>منشور</option>
                                    <option value="archived" @selected(old('status', $announcement->status) === 'archived')>مؤرشف</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="admin-form-label" for="target_type">الاستهداف <span class="text-danger">*</span></label>
                                <select id="target_type" name="target_type" required
                                        class="form-select @error('target_type') is-invalid @enderror">
                                    <option value="all" @selected(old('target_type', $announcement->target_type) === 'all')>الجميع</option>
                                    <option value="department" @selected(old('target_type', $announcement->target_type) === 'department')>قسم محدد</option>
                                    <option value="branch" @selected(old('target_type', $announcement->target_type) === 'branch')>فرع محدد</option>
                                </select>
                                @error('target_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- يُظهرهما/يخفيهما سكربت أسفل الصفحة حسب قيمة الاستهداف --}}
                            <div class="col-md-6" id="wrap_department" @if(old('target_type', $announcement->target_type) !== 'department') hidden @endif>
                                <label class="admin-form-label" for="department_id">القسم</label>
                                <select id="department_id" name="department_id" class="form-select">
                                    <option value="">— اختر القسم —</option>
                                    @foreach ($departments as $dept)
                                        <option value="{{ $dept->id }}" @selected(old('department_id', $announcement->department_id) == $dept->id)>
                                            {{ $dept->name_ar ?? $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6" id="wrap_branch" @if(old('target_type', $announcement->target_type) !== 'branch') hidden @endif>
                                <label class="admin-form-label" for="branch_id">الفرع</label>
                                <select id="branch_id" name="branch_id" class="form-select">
                                    <option value="">— اختر الفرع —</option>
                                    @foreach ($branches as $br)
                                        <option value="{{ $br->id }}" @selected(old('branch_id', $announcement->branch_id) == $br->id)>
                                            {{ $br->name_ar ?? $br->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="admin-form-footer">
                        <a href="{{ route('admin.announcements.index') }}" class="admin-btn admin-btn-secondary">إلغاء</a>
                        <button type="submit" class="admin-btn admin-btn-primary">
                            <i class="ri-save-line"></i>
                            حفظ التعديلات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        /*
         * إظهار حقل القسم أو الفرع حسب نوع الاستهداف.
         * نستخدم السمة hidden لا style.display: تتوافق مع شبكة Bootstrap
         * (col-md-6) التي تضبط display بنفسها، فإعادتها إلى 'block' كانت
         * تكسر السلوك المتجاوب.
         */
        (function () {
            const target = document.getElementById('target_type');
            const wraps = {
                department: document.getElementById('wrap_department'),
                branch: document.getElementById('wrap_branch'),
            };

            if (!target) {
                return;
            }

            target.addEventListener('change', function () {
                Object.keys(wraps).forEach(function (key) {
                    if (wraps[key]) {
                        wraps[key].hidden = target.value !== key;
                    }
                });
            });
        })();
    </script>
@stop
