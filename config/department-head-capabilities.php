<?php

/**
 * شرح قدرات رئيس القسم للواجهة الإدارية (صفحة التوضيح).
 * كل action مرتبط بصلاحية Spatie إن وُجدت؛ otherwise يُقيَّم حسب الدور/النطاق.
 */
return [

    'scope' => [
        'title' => 'نطاق البيانات',
        'description' => 'رئيس القسم يرى ويدير بيانات الموظفين والطلبات المرتبطة بالأقسام التي يديرها مباشرة، بالإضافة إلى الأقسام الفرعية التابعة لها، ومرؤوسيه المباشرين إن وُجدوا.',
    ],

    'portal' => [
        [
            'label' => 'لوحة إدارة الفريق',
            'description' => 'لوحة موحّدة لأعضاء الفريق والموافقات والهيكل.',
            'route' => 'admin.team.dashboard',
            'requires_role' => 'department_head',
        ],
        [
            'label' => 'أعضاء الفريق',
            'description' => 'قائمة الموظفين ضمن نطاق إدارته.',
            'route' => 'admin.team.members',
            'requires_role' => 'department_head',
        ],
        [
            'label' => 'موافقات الفريق',
            'description' => 'طلبات إجازات ومصروفات وتقييمات بانتظار موافقته.',
            'route' => 'admin.team.approvals',
            'requires_role' => 'department_head',
        ],
        [
            'label' => 'هيكل الأقسام',
            'description' => 'عرض هيكل الأقسام التي يديرها.',
            'route' => 'admin.team.structure',
            'requires_role' => 'department_head',
        ],
        [
            'label' => 'تفويض الموافقات',
            'description' => 'تفويض صلاحية الموافقة لموظف آخر مؤقتاً.',
            'route' => 'admin.team.delegations.index',
            'requires_role' => 'department_head',
        ],
        [
            'label' => 'البوابة الذاتية (/employee)',
            'description' => 'إن كان مرتبطاً بسجل موظف — حضوره، إجازاته، وملفه الشخصي.',
            'route' => null,
            'requires_role' => 'employee',
        ],
    ],

    'limitations' => [
        'لا يصل إلى إعدادات النظام أو إدارة المستخدمين والأدوار (ما لم تُمنح صلاحيات إضافية).',
        'لا يُنشئ أو يحذف أقساماً أو يعدّل هيكل الشركة الكامل.',
        'لا يدير الرواتب وكشوف المرتبات إلا إن مُنحت صلاحيات payroll/salary صراحة.',
        'البيانات مقيّدة بنطاق الأقسام المُدارة — لا يرى موظفي أقسام أخرى.',
    ],

    'groups' => [
        [
            'title' => 'الموظفون',
            'icon' => 'ri-team-line',
            'summary' => 'الاطلاع على موظفي الأقسام التابعة له.',
            'actions' => [
                ['label' => 'عرض قائمة الموظفين', 'permission' => 'employee-list'],
                ['label' => 'عرض تفاصيل ملف موظف', 'permission' => 'employee-show'],
            ],
        ],
        [
            'title' => 'الإجازات',
            'icon' => 'ri-calendar-check-line',
            'summary' => 'متابعة طلبات الإجازة والموافقة عليها ضمن فريقه.',
            'actions' => [
                ['label' => 'عرض طلبات الإجازة', 'permission' => 'leave-request-list'],
                ['label' => 'عرض تفاصيل طلب إجازة', 'permission' => 'leave-request-show'],
                ['label' => 'الموافقة أو رفض طلب إجازة', 'permission' => 'leave-request-approve'],
            ],
        ],
        [
            'title' => 'الحضور والانصراف',
            'icon' => 'ri-time-line',
            'summary' => 'متابعة سجلات حضور موظفي نطاقه.',
            'actions' => [
                ['label' => 'عرض سجلات الحضور', 'permission' => 'attendance-list'],
                ['label' => 'عرض تفاصيل سجل حضور', 'permission' => 'attendance-show'],
            ],
        ],
        [
            'title' => 'المصروفات',
            'icon' => 'ri-money-dollar-circle-line',
            'summary' => 'مراجعة طلبات المصروفات والموافقة عليها.',
            'actions' => [
                ['label' => 'عرض طلبات المصروفات', 'permission' => 'expense-request-list'],
                ['label' => 'عرض تفاصيل طلب مصروف', 'permission' => 'expense-request-show'],
                ['label' => 'الموافقة على طلب مصروف', 'permission' => 'expense-request-approve'],
            ],
        ],
        [
            'title' => 'التقييمات',
            'icon' => 'ri-star-line',
            'summary' => 'متابعة تقييمات الأداء والموافقة عليها.',
            'actions' => [
                ['label' => 'عرض التقييمات', 'permission' => 'performance-review-list'],
                ['label' => 'عرض تفاصيل تقييم', 'permission' => 'performance-review-show'],
                ['label' => 'الموافقة على تقييم', 'permission' => 'performance-review-approve'],
            ],
        ],
        [
            'title' => 'مركز الموافقات وسير العمل',
            'icon' => 'ri-checkbox-circle-line',
            'summary' => 'الموافقات العامة والتغييرات الوظيفية ضمن صلاحياته.',
            'actions' => [
                ['label' => 'عرض قائمة الموافقات', 'permission' => 'approval-list'],
                ['label' => 'عرض تفاصيل موافقة', 'permission' => 'approval-show'],
                ['label' => 'عرض طلبات التغيير الوظيفي', 'permission' => 'employee-job-change-list'],
                ['label' => 'عرض تفاصيل تغيير وظيفي', 'permission' => 'employee-job-change-show'],
            ],
        ],
        [
            'title' => 'التقارير',
            'icon' => 'ri-bar-chart-box-line',
            'summary' => 'تقارير تشغيلية ضمن نطاق القسم (حسب الصلاحيات الممنوحة).',
            'actions' => [
                ['label' => 'الوصول إلى التقارير', 'permission' => 'report-view'],
                ['label' => 'تقرير الموظفين', 'permission' => 'report-employees'],
                ['label' => 'تقرير الحضور', 'permission' => 'report-attendance'],
                ['label' => 'تقرير الإجازات', 'permission' => 'report-leaves'],
                ['label' => 'تقرير الأداء', 'permission' => 'report-performance'],
                ['label' => 'لوحة تقارير', 'permission' => 'report-dashboard'],
                ['label' => 'مؤشرات KPI', 'permission' => 'report-kpis'],
            ],
        ],
        [
            'title' => 'النظام والإشعارات',
            'icon' => 'ri-notification-3-line',
            'summary' => 'لوحة التحكم والإشعارات الإدارية.',
            'actions' => [
                ['label' => 'عرض لوحة التحكم الإدارية', 'permission' => 'dashboard-view'],
                ['label' => 'عرض الإشعارات', 'permission' => 'notification-list'],
                ['label' => 'تعليم الإشعار كمقروء', 'permission' => 'notification-mark-read'],
            ],
        ],
    ],

];
