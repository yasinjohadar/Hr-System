{{--
    لوحة الهيدر الجانبية.

    كانت هذه اللوحة (من قالب Ynex الأصلي) ثلاثة تبويبات مزيّفة بالكامل:
    "Chat" برسائل نصّية ثابتة («New Websites is Created»)، "Notifications"
    بصور أشخاص من مجلد assets/images/faces لا علاقة لهم بالنظام، و"Friends"
    بأسماء وهمية («Mozelle Belt», «Florinda Carasco») تتكرّر عشوائياً وتفتح
    زرّ رسائل يستهدف #chatmodel — مودال غير مُضمَّن في الصفحة أصلاً
    (admin/layouts/modal-video-audio.blade.php لا يُستدعى من master.blade.php)،
    فالزرّ لم يكن يفعل شيئاً عند الضغط عليه.

    استُبدلت بتبويبين ببيانات حقيقية من admin.header-panel.people:
    من يستخدم النظام الآن (حسب نشاط الجلسة الفعلي)، ومن يملك صلاحيات إدارية.
    تُجلب فقط عند فتح اللوحة (لا حمل إضافي على كل تحميل صفحة).
--}}
<div class="offcanvas offcanvas-end hr-panel" tabindex="-1" id="header-sidebar"
     data-people-url="{{ route('admin.header-panel.people') }}"
     aria-labelledby="sidebarLabel">
    <div class="offcanvas-header hr-panel__header">
        <h5 class="mb-0" id="sidebarLabel">نشاط الفريق</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="إغلاق"></button>
    </div>

    <div class="offcanvas-body hr-panel__body p-0">
        <ul class="nav hr-panel__tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" data-bs-toggle="tab" data-bs-target="#hp-active" href="#hp-active"
                   role="tab" aria-selected="true">
                    <i class="ri-flashlight-line"></i>
                    نشطون الآن
                    <span class="hr-panel__count" id="hp-active-count" hidden>0</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" data-bs-target="#hp-managers" href="#hp-managers"
                   role="tab" aria-selected="false">
                    <i class="ri-shield-star-line"></i>
                    الإدارة
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active show" id="hp-active" role="tabpanel">
                <div class="hr-panel__list" id="hp-active-list" data-hp-empty="لا يوجد أحد نشط الآن">
                    {{-- تُملأ عبر admin-header-panel.js عند فتح اللوحة --}}
                </div>
            </div>
            <div class="tab-pane" id="hp-managers" role="tabpanel">
                <div class="hr-panel__list" id="hp-managers-list" data-hp-empty="لا يوجد مسؤولون مسجّلون">
                    {{-- تُملأ عبر admin-header-panel.js عند فتح اللوحة --}}
                </div>
            </div>
        </div>
    </div>
</div>
