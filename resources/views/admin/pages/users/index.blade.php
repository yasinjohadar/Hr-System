@extends('admin.layouts.master')

@section('page-title')
    قائمة المستخدمون
@stop



@section('css')
<style>
    .actions-cell {
        position: relative;
        vertical-align: middle;
        text-align: right;
    }
    .actions-dropdown-btn {
        width: 34px;
        height: 34px;
        border-radius: 5px !important;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .actions-dropdown-btn::after {
        display: none;
    }
    .users-table-wrapper {
        overflow: visible !important;
    }
    .users-table-wrapper .table-responsive {
        overflow: visible !important;
    }
    .dropdown-menu-actions {
        border-radius: 5px !important;
        min-width: 185px;
        padding: 6px 0;
        margin-top: 4px !important;
        z-index: 1060;
    }
    .dropdown-menu-actions .dropdown-item {
        padding: 8px 14px;
        font-size: 14px;
        transition: background .15s;
    }
    .dropdown-menu-actions .dropdown-item:hover {
        background-color: #f5f6f8;
    }
    .dropdown-menu-actions .dropdown-item i {
        width: 18px;
        text-align: center;
    }
    .dropdown-menu-actions .dropdown-divider {
        margin: 4px 0;
    }
</style>
@stop

@section('content')
    @if (\Session::has('success'))
        <div class="alert alert-success">
            <ul>
                <li>{!! \Session::get('success') !!}</li>
            </ul>
        </div>
    @endif

    @if (\Session::has('error'))
        <div class="alert alert-danger">
            <ul>
                <li>{!! \Session::get('error') !!}</li>
            </ul>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <!-- Start::app-content -->
    <div class="main-content app-content">
        <div class="container-fluid">

            <!-- Page Header -->
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">كافة المستخدمين</h5>

                </div>


            </div>
            <!-- Page Header Close -->



            <!-- Start::row-1 -->
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex gap-3">
                            <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">إنشاء مستخدم جديد</a>
                        </div>


                        <div class="card-body">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <form onsubmit="return false" class="d-flex align-items-center gap-2">
                                    <input style="width: 300px" type="text" id="liveSearch" class="form-control"
                                        placeholder="بحث بالاسم أو الإيميل أو الهاتف" value="{{ request('query') }}" autocomplete="off">
                                    <select id="filterActive" class="form-select" style="width:150px;">
                                        <option value="">كل الحالات النشطة</option>
                                        <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>نشط</option>
                                        <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>غير نشط</option>
                                    </select>
                                    <select id="filterStatus" class="form-select" style="width:140px;">
                                        <option value="">كل الحالات</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>مفعل</option>
                                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>موقوف</option>
                                        <option value="banned" {{ request('status') == 'banned' ? 'selected' : '' }}>محظور</option>
                                    </select>
                                </form>
                                <a href="{{ route('users.index') }}" class="btn btn-danger btn-sm">مسح</a>
                                <span id="searchSpinner" class="spinner-border spinner-border-sm text-primary d-none" role="status"></span>
                            </div>

                            <div id="usersTableContainer">
                                @include('admin.pages.users._table')
                            </div>



                        </div><!-- end card-body -->
                    </div><!-- end card -->
                </div>
            </div>
            <!--End::row-1 -->


        </div>
    </div>

    <div class="modal fade" id="toggleStatusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:480px;">
            <div class="modal-content border-0" style="border-radius:5px;box-shadow:0 4px 20px rgba(0,0,0,.08);">
                <div class="modal-body text-center px-4 pt-5 pb-4">
                    <div class="mb-3">
                        <span id="toggleModalIcon" class="d-inline-flex align-items-center justify-content-center bg-warning bg-opacity-10" style="width:56px;height:56px;border-radius:50%;">
                            <i class="fas fa-arrow-right-arrow-left text-warning fs-4"></i>
                        </span>
                    </div>
                    <h5 class="fw-bold mb-2">تأكيد تغيير الحالة</h5>
                    <p class="text-muted mb-1 fs-15">
                        هل تريد تغيير حالة المستخدم
                        <strong id="toggleModalUserName" class="text-dark"></strong>
                        ؟
                    </p>
                    <p class="mb-0 mt-2" id="toggleModalNewStatus"></p>
                </div>
                <div class="modal-footer justify-content-center border-0 gap-3 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal" style="min-width:120px;border-radius:5px;">
                        إلغاء
                    </button>
                    <button type="button" class="btn btn-primary px-4" id="toggleModalConfirm" style="min-width:140px;border-radius:5px;">
                        <i class="fas fa-check me-1"></i>تأكيد التغيير
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="loginCodeModal" tabindex="-1" aria-labelledby="loginCodeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginCodeModalLabel">كود دخول لمتصفح آخر</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        صالح لمدة 15 دقيقة ولا يعمل إلا مرة واحدة.
                        <br>المستخدم: <strong id="loginCodeUserName"></strong>
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-bold">الكود</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="loginCodeValue" readonly>
                            <button class="btn btn-outline-secondary" type="button" id="copyCodeBtn" title="نسخ الكود">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold">الرابط</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="loginCodeUrl" readonly>
                            <button class="btn btn-outline-secondary" type="button" id="copyUrlBtn" title="نسخ الرابط">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



@stop

@section('js')
<script>
// تأكد من تحميل الصفحة
console.log('Page loaded, initializing toggle switches...');

document.addEventListener('DOMContentLoaded', function() {
    initializeToggleSwitches();
    setupLoginCodeButtons();
});

// دالة تهيئة التبديل
function initializeToggleSwitches() {
    const buttons = document.querySelectorAll('.toggle-status-btn');
    if (buttons.length === 0) return;

    const toggleModal = new bootstrap.Modal(document.getElementById('toggleStatusModal'));
    const modalUserName = document.getElementById('toggleModalUserName');
    const modalNewStatus = document.getElementById('toggleModalNewStatus');
    const modalConfirm = document.getElementById('toggleModalConfirm');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    let pendingBtn = null;

    buttons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            const isActive = this.dataset.isActive === '1';
            const userName = this.dataset.userName;
            const willBeActive = !isActive;
            const newStatusText = willBeActive ? 'نشط' : 'غير نشط';

            modalUserName.textContent = userName;
            modalNewStatus.innerHTML = 'سيتم تحويل الحالة إلى <span class="badge bg-' + (willBeActive ? 'success' : 'secondary') + ' fs-14">' + newStatusText + '</span>';
            toggleModalConfirm.className = 'btn px-4 ' + (willBeActive ? 'btn-success' : 'btn-danger');
            toggleModalConfirm.innerHTML = '<i class="fas fa-check me-1"></i>تأكيد التغيير';

            pendingBtn = this;
            toggleModal.show();
        });
    });

    modalConfirm.addEventListener('click', function () {
        if (!pendingBtn) return;
        const btn = pendingBtn;
        const userId = btn.dataset.userId;
        const isActive = btn.dataset.isActive === '1';
        const willBeActive = !isActive;

        btn.disabled = true;
        toggleModal.hide();

        fetch('/users/' + userId + '/toggle-status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ is_active: willBeActive })
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                btn.dataset.isActive = data.is_active ? '1' : '0';
                btn.textContent = data.is_active ? 'مفعل' : 'غير مفعل';
                btn.className = 'btn btn-sm toggle-status-btn ' + (data.is_active ? 'btn-success' : 'btn-secondary');
                showAlert(data.message || 'تم تحديث حالة المستخدم بنجاح', 'success');
            } else {
                showAlert(data.message || 'حدث خطأ', 'error');
            }
            btn.disabled = false;
            pendingBtn = null;
        })
        .catch(function () {
            showAlert('حدث خطأ أثناء تحديث حالة المستخدم', 'error');
            btn.disabled = false;
            pendingBtn = null;
        });
    });

    document.getElementById('toggleStatusModal').addEventListener('hidden.bs.modal', function () {
        if (pendingBtn) {
            pendingBtn.disabled = false;
            pendingBtn = null;
        }
    });
}

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-' + (type === 'success' ? 'success' : 'danger') + ' alert-dismissible fade show';
    alertDiv.innerHTML = message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';

    const container = document.querySelector('.main-content');
    if (container) {
        container.insertBefore(alertDiv, container.firstChild);
    } else {
        document.body.insertBefore(alertDiv, document.body.firstChild);
    }

    setTimeout(function () {
        if (alertDiv.parentNode) alertDiv.remove();
    }, 3000);
}
        
        var searchTimer = null;
        var searchInput = document.getElementById('liveSearch');
        var filterActive = document.getElementById('filterActive');
        var filterStatus = document.getElementById('filterStatus');
        var spinner = document.getElementById('searchSpinner');
        var container = document.getElementById('usersTableContainer');

        function doSearch() {
            spinner.classList.remove('d-none');
            var params = new URLSearchParams();
            var q = searchInput ? searchInput.value : '';
            if (q) params.append('q', q);
            if (filterActive && filterActive.value) params.append('is_active', filterActive.value);
            if (filterStatus && filterStatus.value) params.append('status', filterStatus.value);

            fetch('/admin/users/search?' + params.toString(), {
                headers: { 'Accept': 'text/html', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function (r) { return r.text(); })
            .then(function (html) {
                container.innerHTML = html;
                spinner.classList.add('d-none');
                initializeToggleSwitches();
                setupLoginCodeButtons();
            })
            .catch(function () {
                spinner.classList.add('d-none');
            });
        }

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(doSearch, 300);
            });
        }
        if (filterActive) filterActive.addEventListener('change', doSearch);
        if (filterStatus) filterStatus.addEventListener('change', doSearch);

        function setupLoginCodeButtons() {
            var modal = bootstrap.Modal.getInstance(document.getElementById('loginCodeModal'));
            if (!modal) modal = new bootstrap.Modal(document.getElementById('loginCodeModal'));
            var codeInput = document.getElementById('loginCodeValue');
            var urlInput = document.getElementById('loginCodeUrl');
            var userNameEl = document.getElementById('loginCodeUserName');
            var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            document.querySelectorAll('.login-code-btn').forEach(function (btn) {
                if (btn.dataset.loginBound) return;
                btn.dataset.loginBound = '1';
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    var userId = this.dataset.userId;
                    var userName = this.dataset.userName;
                    userNameEl.textContent = userName;
                    this.style.pointerEvents = 'none';
                    this.style.opacity = '0.6';

                    fetch('/admin/users/' + userId + '/login-code', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({})
                    })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        if (data.error) { alert(data.error); this.style.pointerEvents = ''; this.style.opacity = ''; return; }
                        codeInput.value = data.code || '';
                        urlInput.value = data.url || '';
                        modal.show();
                        this.style.pointerEvents = '';
                        this.style.opacity = '';
                    }.bind(this))
                    .catch(function () {
                        alert('حدث خطأ أثناء إنشاء الكود.');
                        this.style.pointerEvents = '';
                        this.style.opacity = '';
                    }.bind(this));
                });
            });
        }

        setupLoginCodeButtons();

        document.addEventListener('click', function (e) {
            var btn = e.target.closest('.copy-email-btn');
            if (!btn) return;
            navigator.clipboard.writeText(btn.dataset.email).then(function () {
                var icon = btn.querySelector('i');
                icon.classList.remove('fa-copy', 'far');
                icon.classList.add('fas', 'fa-check', 'text-success');
                setTimeout(function () {
                    icon.classList.remove('fa-check', 'text-success', 'fas');
                    icon.classList.add('far', 'fa-copy');
                }, 1500);
            });
        });
        </script>
        
        <script>
        document.getElementById('copyCodeBtn').addEventListener('click', function () {
            var el = document.getElementById('loginCodeValue');
            el.select();
            navigator.clipboard.writeText(el.value).catch(function () { document.execCommand('copy'); });
        });
        document.getElementById('copyUrlBtn').addEventListener('click', function () {
            var el = document.getElementById('loginCodeUrl');
            el.select();
            navigator.clipboard.writeText(el.value).catch(function () { document.execCommand('copy'); });
        });
        </script>
        
        <script>
        document.querySelectorAll('.toggle-password').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var input = document.getElementById(this.dataset.target);
                var icon = this.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.replace('fa-eye', 'fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.replace('fa-eye-slash', 'fa-eye');
                }
            });
        });
        </script>
@stop
