@php
    $editorSteps = $editorSteps ?? [];
    $roles = $roles ?? collect();
    $users = $users ?? collect();
    $defaultTemplate = $defaultTemplate ?? [];
    $hasActiveInstances = $hasActiveInstances ?? false;
@endphp

<div class="workflow-steps-editor mt-4" data-workflow-steps-editor>
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h6 class="fw-bold mb-1">خطوات الموافقة</h6>
            <p class="text-muted fs-13 mb-0">رتّب الموافقات من الأول إلى الأخير. يُنصح أن تكون الخطوة الأولى <strong>مدير القسم</strong> لطلبات الموظفين.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm" data-load-default-template>
                <i class="ri-file-copy-line me-1"></i>تحميل القالب الافتراضي
            </button>
            <button type="button" class="btn btn-outline-primary btn-sm" data-add-step>
                <i class="ri-add-line me-1"></i>إضافة خطوة
            </button>
        </div>
    </div>

    @if ($hasActiveInstances)
        <div class="alert alert-warning py-2 fs-13 mb-3">
            يوجد طلبات قيد الموافقة على هذا السير. تعديل الخطوات يطبّق على <strong>الطلبات الجديدة</strong> فقط، ولا يمكن حذف خطوة مستخدمة حالياً.
        </div>
    @endif

    @error('steps')
        <div class="alert alert-danger py-2">{{ $message }}</div>
    @enderror

    <div class="workflow-steps-list" data-steps-list>
        @forelse ($editorSteps as $index => $step)
            @include('admin.pages.workflows.partials.workflow-step-row', [
                'index' => $index,
                'step' => $step,
                'roles' => $roles,
                'users' => $users,
            ])
        @empty
        @endforelse
    </div>

    <p class="text-muted fs-12 mb-0 mt-2" data-empty-hint @if(count($editorSteps) > 0) style="display:none" @endif>
        لا توجد خطوات. أضف خطوة واحدة على الأقل أو حمّل القالب الافتراضي.
    </p>
</div>

<template id="workflow-step-row-template">
    @include('admin.pages.workflows.partials.workflow-step-row', [
        'index' => '__INDEX__',
        'step' => [
            'name_ar' => '',
            'name' => '',
            'approver_type' => 'department_manager',
            'role_id' => null,
            'approver_id' => null,
            'is_required' => true,
            'can_reject' => true,
        ],
        'roles' => $roles,
        'users' => $users,
    ])
</template>

<script type="application/json" id="workflow-default-template-json">@json($defaultTemplate)</script>

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin-workflows.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/admin-workflow-steps.js') }}"></script>
@endpush
