<div class="filters-card card custom-card border-0 shadow-none mb-4">
    <div class="card-body py-3">
        <form id="employees-filter-form" method="GET" action="{{ route('admin.employees.index') }}"
            class="d-flex flex-wrap align-items-end gap-2">
            <div class="search-input-wrap flex-grow-1" style="min-width: 200px; max-width: 320px;">
                <i class="ri-search-line search-icon"></i>
                <input type="text" name="query" id="employees-filter-query" class="form-control"
                    placeholder="بحث بالاسم أو الرقم أو البريد" value="{{ request('query') }}" autocomplete="off">
            </div>
            <select name="department_id" id="employees-filter-department" class="form-select" style="width: 150px;">
                <option value="">كل الأقسام</option>
                @foreach ($departments as $dept)
                    <option value="{{ $dept->id }}"
                        {{ (string) request('department_id') === (string) $dept->id ? 'selected' : '' }}>
                        {{ $dept->name }}
                    </option>
                @endforeach
            </select>
            <select name="position_id" id="employees-filter-position" class="form-select" style="width: 150px;">
                <option value="">كل المناصب</option>
                @foreach ($positions as $pos)
                    <option value="{{ $pos->id }}"
                        {{ (string) request('position_id') === (string) $pos->id ? 'selected' : '' }}>
                        {{ $pos->title }}
                    </option>
                @endforeach
            </select>
            <select name="employment_status" id="employees-filter-employment" class="form-select" style="width: 150px;">
                <option value="">الحالة الوظيفية</option>
                <option value="active" {{ request('employment_status') == 'active' ? 'selected' : '' }}>نشط</option>
                <option value="on_leave" {{ request('employment_status') == 'on_leave' ? 'selected' : '' }}>في إجازة</option>
                <option value="terminated" {{ request('employment_status') == 'terminated' ? 'selected' : '' }}>منتهي</option>
                <option value="resigned" {{ request('employment_status') == 'resigned' ? 'selected' : '' }}>استقال</option>
            </select>
            <select name="is_active" id="employees-filter-active" class="form-select" style="width: 140px;">
                <option value="">كل التفعيل</option>
                <option value="1" {{ request('is_active') === '1' || request('is_active') === 1 ? 'selected' : '' }}>مفعّل</option>
                <option value="0" {{ request('is_active') === '0' || request('is_active') === 0 ? 'selected' : '' }}>معطّل</option>
            </select>
            <button type="button" class="btn btn-outline-danger btn-sm" id="employees-filter-clear">
                <i class="ri-filter-off-line me-1"></i>مسح
            </button>
            <span id="employees-loading" class="spinner-border spinner-border-sm text-primary d-none" role="status"></span>
        </form>
    </div>
</div>
