@extends('admin.layouts.master')

@section('page-title')
    تفاصيل الموظف
@stop

@section('css')
    <style>
        .employee-show-hero {
            background: linear-gradient(145deg, var(--bs-primary) 0%, rgba(var(--bs-primary-rgb), 0.88) 55%, rgb(15, 76, 129) 100%);
            color: #fff;
            border: none;
        }
        .employee-show-hero .text-white-75 { color: rgba(255,255,255,.85) !important; }
    </style>
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل الموظف</h5>
                    <p class="text-muted small mb-0">{{ $employee->full_name }} — {{ $employee->employee_code }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                    <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-right me-1"></i>العودة للقائمة
                    </a>
                    @can('employee-edit')
                        <a href="{{ route('admin.employees.edit', $employee->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>تعديل
                        </a>
                    @endcan
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-4">
                    <div class="card employee-show-hero shadow-sm h-100">
                        <div class="card-body d-flex flex-column text-center">
                            <div class="mb-3">
                                @if($employee->photo)
                                    <img src="{{ asset('storage/' . $employee->photo) }}" alt="صورة الموظف" class="rounded-circle border border-white border-3" style="width:100px;height:100px;object-fit:cover;">
                                @else
                                    <span class="avatar bg-white bg-opacity-25 rounded-circle d-inline-flex align-items-center justify-content-center" style="width:100px;height:100px;">
                                        <i class="fas fa-user fs-1"></i>
                                    </span>
                                @endif
                            </div>
                            <div class="fw-bold fs-5">{{ $employee->full_name }}</div>
                            <div class="small text-white-75 font-monospace mb-3">{{ $employee->employee_code }}</div>
                            <div class="mb-3">
                                <span class="badge bg-{{ $employee->is_active ? 'success' : 'danger' }} fs-14 px-3 py-2">
                                    {{ $employee->is_active ? 'نشط' : 'غير نشط' }}
                                </span>
                            </div>
                            <div class="mb-3 pb-3 border-bottom border-white border-opacity-25">
                                <div class="text-white-75 small mb-1"><i class="fas fa-building me-1"></i>القسم</div>
                                <div class="fw-semibold">{{ $employee->department->name ?? '—' }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="text-white-75 small mb-1"><i class="fas fa-briefcase me-1"></i>المنصب</div>
                                <div class="fw-semibold">{{ $employee->position->title ?? '—' }}</div>
                            </div>

                            @if($employee->user_id && $employee->user && $employee->user->is_active)
                            <div class="mt-auto pt-3 border-top border-white border-opacity-25">
                                @can('employee-show')
                                <div class="d-flex flex-column gap-2">
                                    <form action="{{ route('admin.employees.login-as', $employee) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-light btn-sm w-100">
                                            <i class="fas fa-user-secret me-1"></i>الدخول كموظف
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-outline-light btn-sm w-100" id="btnLoginCode"
                                            data-url="{{ route('admin.employees.login-code', $employee) }}">
                                        <i class="fas fa-link me-1"></i>كود دخول لمتصفح آخر
                                    </button>
                                </div>
                                @endcan
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-circle-info text-primary me-2"></i>معلومات الوظيفة
                            </h6>
                            <small class="text-muted">القسم، المنصب، الفرع، والراتب</small>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <tbody>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle" style="width:40%">
                                                <i class="fas fa-building text-muted me-2"></i>القسم
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $employee->department->name ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-briefcase text-muted me-2"></i>المنصب
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $employee->position->title ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-code-branch text-muted me-2"></i>الفرع
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $employee->branch->name ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-user-tie text-muted me-2"></i>المدير المباشر
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $employee->manager->full_name ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="far fa-calendar-check text-muted me-2"></i>تاريخ التوظيف
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $employee->hire_date ? $employee->hire_date->format('Y-m-d') : '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-clock text-muted me-2"></i>نوع التوظيف
                                            </th>
                                            <td class="pe-4 py-3 align-middle">
                                                <span class="badge bg-info-subtle text-dark border fs-14">{{ $employee->employment_type_name_ar ?? $employee->employment_type ?? '—' }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-money-bill-wave text-muted me-2"></i>الراتب الأساسي
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $employee->base_salary ? number_format($employee->base_salary, 2) . ' ر.س' : '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-flag text-muted me-2"></i>الحالة الوظيفية
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $employee->employment_status_name_ar ?? $employee->employment_status ?? '—' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-address-book text-primary me-2"></i>معلومات الاتصال
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <tbody>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle" style="width:40%">
                                                <i class="fas fa-envelope text-muted me-2"></i>البريد الإلكتروني
                                            </th>
                                            <td class="pe-4 py-3 align-middle font-monospace small">{{ $employee->user->email ?? $employee->personal_email ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-phone text-muted me-2"></i>الهاتف
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $employee->phone ?? $employee->personal_phone ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-map-marker-alt text-muted me-2"></i>العنوان
                                            </th>
                                            <td class="pe-4 py-3 align-middle">{{ $employee->address ?? '—' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-id-card text-primary me-2"></i>المعلومات الشخصية
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <tbody>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle" style="width:40%">
                                                <i class="fas fa-cake-candles text-muted me-2"></i>تاريخ الميلاد
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $employee->date_of_birth ? $employee->date_of_birth->format('Y-m-d') : '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-venus-mars text-muted me-2"></i>الجنس
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $employee->gender == 'male' ? 'ذكر' : ($employee->gender == 'female' ? 'أنثى' : '—') }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-globe text-muted me-2"></i>الجنسية
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $employee->nationality ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-ring text-muted me-2"></i>الحالة الاجتماعية
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ $employee->marital_status_name_ar ?? $employee->marital_status ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-fingerprint text-muted me-2"></i>رقم الهوية
                                            </th>
                                            <td class="pe-4 py-3 align-middle font-monospace small">{{ $employee->national_id ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-passport text-muted me-2"></i>رقم الجواز
                                            </th>
                                            <td class="pe-4 py-3 align-middle font-monospace small">{{ $employee->passport_number ?? '—' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($employee->emergency_contact_name)
            <div class="row g-3 mt-1">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-phone-volume text-danger me-2"></i>جهة الاتصال في حالات الطوارئ
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row row-cols-1 row-cols-md-3 g-0">
                                <div class="col border-bottom border-end-md p-3">
                                    <div class="small text-muted mb-1"><i class="fas fa-user me-1"></i>الاسم</div>
                                    <div class="fw-semibold">{{ $employee->emergency_contact_name }}</div>
                                </div>
                                <div class="col border-bottom border-end-md p-3">
                                    <div class="small text-muted mb-1"><i class="fas fa-phone me-1"></i>الهاتف</div>
                                    <div class="fw-semibold">{{ $employee->emergency_contact_phone ?? '—' }}</div>
                                </div>
                                <div class="col border-bottom p-3">
                                    <div class="small text-muted mb-1"><i class="fas fa-users-between-lines me-1"></i>العلاقة</div>
                                    <div class="fw-semibold">{{ $employee->emergency_contact_relation ?? '—' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="row g-3 mt-1">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light py-3 border-bottom d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="mb-0 fw-semibold">
                                    <i class="fas fa-arrow-right-arrow-left text-primary me-2"></i>سجل التغييرات الوظيفية
                                </h6>
                                <small class="text-muted">آخر التغييرات الوظيفية المعتمدة</small>
                            </div>
                            <a href="{{ route('admin.employee-job-changes.index', ['employee_id' => $employee->id]) }}" class="btn btn-outline-primary btn-sm">
                                عرض الكل
                            </a>
                        </div>
                        <div class="card-body p-0">
                            @if(isset($jobChangeHistory) && $jobChangeHistory->isNotEmpty())
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="ps-4">التاريخ الفعال</th>
                                                <th>نوع التغيير</th>
                                                <th>ملخص التغيير</th>
                                                <th class="pe-4"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($jobChangeHistory as $change)
                                                <tr>
                                                    <td class="ps-4 align-middle font-monospace small">{{ $change->effective_date->format('Y-m-d') }}</td>
                                                    <td class="align-middle">
                                                        <span class="badge bg-info-subtle text-dark border">{{ $change->change_type_label }}</span>
                                                    </td>
                                                    <td class="align-middle">
                                                        @php
                                                            $parts = [];
                                                            if ($change->new_department_id && ($change->old_department_id != $change->new_department_id))
                                                                $parts[] = 'القسم: ' . ($change->oldDepartment->name ?? '-') . ' → ' . ($change->newDepartment->name ?? '-');
                                                            if ($change->new_position_id && ($change->old_position_id != $change->new_position_id))
                                                                $parts[] = 'المنصب: ' . ($change->oldPosition->title ?? '-') . ' → ' . ($change->newPosition->title ?? '-');
                                                            if ($change->new_branch_id && ($change->old_branch_id != $change->new_branch_id))
                                                                $parts[] = 'الفرع: ' . ($change->oldBranch->name ?? '-') . ' → ' . ($change->newBranch->name ?? '-');
                                                            if ($change->new_manager_id && ($change->old_manager_id != $change->new_manager_id))
                                                                $parts[] = 'المدير: ' . ($change->oldManager->full_name ?? '-') . ' → ' . ($change->newManager->full_name ?? '-');
                                                            if ($change->new_salary !== null && (string)$change->old_salary !== (string)$change->new_salary)
                                                                $parts[] = 'الراتب: ' . number_format($change->old_salary ?? 0, 2) . ' → ' . number_format($change->new_salary, 2) . ' ر.س';
                                                        @endphp
                                                        @if(count($parts) > 0)
                                                            <span class="small">{{ implode(' | ', $parts) }}</span>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td class="pe-4 align-middle text-end">
                                                        <a href="{{ route('admin.employee-job-changes.show', $change) }}" class="btn btn-sm btn-light">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center text-muted py-4">لا يوجد سجل تغييرات وظيفية.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-1 mb-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light py-3">
                            <h6 class="mb-0 fw-semibold"><i class="fas fa-clock-rotate-left text-primary me-2"></i>بيانات السجل</h6>
                        </div>
                        <div class="card-body">
                            <div class="row row-cols-1 row-cols-md-3 g-0">
                                <div class="col border-bottom border-end-md p-3">
                                    <div class="small text-muted mb-1"><i class="fas fa-user-pen me-1"></i>أنشأ بواسطة</div>
                                    <div class="fw-semibold">{{ $employee->creator->name ?? '—' }}</div>
                                </div>
                                <div class="col border-bottom border-end-md p-3">
                                    <div class="small text-muted mb-1"><i class="far fa-clock me-1"></i>تاريخ الإنشاء</div>
                                    <div class="fw-semibold font-monospace small">{{ $employee->created_at->format('Y-m-d H:i') }}</div>
                                </div>
                                <div class="col border-bottom p-3">
                                    <div class="small text-muted mb-1"><i class="fas fa-pen-to-square me-1"></i>آخر تحديث</div>
                                    <div class="fw-semibold font-monospace small">{{ $employee->updated_at->format('Y-m-d H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
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
                    <p class="text-muted small mb-3">صالح لمدة 15 دقيقة ولا يعمل إلا مرة واحدة. انسخ الرابط وافتحه في المتصفح الآخر.</p>
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
(function() {
    const btn = document.getElementById('btnLoginCode');
    if (!btn) return;
    const modal = new bootstrap.Modal(document.getElementById('loginCodeModal'));
    const codeInput = document.getElementById('loginCodeValue');
    const urlInput = document.getElementById('loginCodeUrl');
    const copyCodeBtn = document.getElementById('copyCodeBtn');
    const copyUrlBtn = document.getElementById('copyUrlBtn');
    const url = btn.getAttribute('data-url');

    btn.addEventListener('click', function() {
        btn.disabled = true;
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({})
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.error) {
                alert(data.error);
                btn.disabled = false;
                return;
            }
            codeInput.value = data.code || '';
            urlInput.value = data.url || '';
            modal.show();
            btn.disabled = false;
        })
        .catch(function() {
            alert('حدث خطأ أثناء إنشاء الكود.');
            btn.disabled = false;
        });
    });

    function copyToClipboard(el) {
        el.select();
        document.execCommand('copy');
    }
    copyCodeBtn.addEventListener('click', function() { copyToClipboard(codeInput); });
    copyUrlBtn.addEventListener('click', function() { copyToClipboard(urlInput); });
})();
</script>
@endsection
