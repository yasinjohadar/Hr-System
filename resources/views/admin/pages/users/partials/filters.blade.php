<div class="filters-card card custom-card border-0 shadow-none mb-4">
    <div class="card-body py-3">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <div class="search-input-wrap flex-grow-1" style="min-width: 220px; max-width: 360px;">
                <i class="ri-search-line search-icon"></i>
                <input type="text" id="liveSearch" class="form-control" placeholder="بحث بالاسم أو الإيميل أو الهاتف"
                    value="{{ request('query') }}" autocomplete="off">
            </div>
            <select id="filterActive" class="form-select" style="width: 160px;">
                <option value="">كل تفعيل الدخول</option>
                <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>دخول مفعّل</option>
                <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>دخول معطّل</option>
            </select>
            <select id="filterStatus" class="form-select" style="width: 150px;">
                <option value="">كل حالات الحساب</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>مفعل</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>موقوف</option>
                <option value="banned" {{ request('status') == 'banned' ? 'selected' : '' }}>محظور</option>
            </select>
            <a href="{{ route('users.index') }}" class="btn btn-outline-danger btn-sm">
                <i class="ri-filter-off-line me-1"></i>مسح
            </a>
            <span id="searchSpinner" class="spinner-border spinner-border-sm text-primary d-none" role="status"></span>
        </div>
    </div>
</div>
