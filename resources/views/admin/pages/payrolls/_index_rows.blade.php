{{--
    صفوف جدول كشوف الرواتب.

    يُصيَّر مرّتين: مع الصفحة الكاملة، وكـ html_rows في ردّ AJAX الفلترة
    (PayrollController@index). لذلك لا يجوز أن يعتمد أي عنصر هنا على
    addEventListener عند التحميل — كل الربط بتفويض الأحداث على document
    (admin-post-action.js و admin-confirm.js).
--}}
@forelse ($payrolls as $payroll)
    <tr>
        <td><span class="admin-badge admin-badge-muted">{{ $payroll->payroll_code }}</span></td>
        <td class="fw-semibold">{{ $payroll->employee->full_name }}</td>
        <td>{{ $payroll->month_name }} / {{ $payroll->payroll_year }}</td>
        <td>{{ number_format($payroll->base_salary, 2) }} {{ $payroll->currency->code ?? '' }}</td>
        <td>{{ number_format($payroll->total_allowances, 2) }}</td>
        <td class="text-danger">{{ number_format($payroll->total_deductions, 2) }}</td>
        <td><strong class="text-success">{{ number_format($payroll->net_salary, 2) }}</strong></td>
        <td>
            {{-- حالات الكشف الخمس مُطابَقة على شارات النظام الموحّدة --}}
            <span class="admin-badge admin-badge-{{ match ($payroll->status) {
                'draft' => 'muted',
                'calculated' => 'role',
                'approved' => 'warning',
                'paid' => 'success',
                'cancelled' => 'danger',
                default => 'muted',
            } }}">
                {{ $payroll->status_name_ar }}
            </span>
        </td>
        <td>
            {{--
                كانت 6 أزرار متجاورة في العمود. جُمعت في قائمة منسدلة
                واحدة كبقية الصفحات؛ عمليتا الحساب والموافقة تبقيان
                نموذجَي POST لكن بتأكيد data-confirm قبل التنفيذ.
            --}}
            <div class="admin-row-actions dropdown">
                <button class="admin-kebab-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ri-more-2-fill"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    @can('payroll-show')
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.payrolls.show', $payroll->id) }}">
                                <i class="ri-eye-line text-info me-2"></i>عرض الكشف
                            </a>
                        </li>
                    @endcan

                    @can('payroll-edit')
                        @if ($payroll->status !== 'paid')
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.payrolls.edit', $payroll->id) }}">
                                    <i class="ri-pencil-line text-primary me-2"></i>تعديل
                                </a>
                            </li>
                        @endif
                    @endcan

                    {{--
                        زرّ بلا نموذج مُعشَّش: admin-post-action.js يبني نموذج POST
                        في document.body عند التأكيد. النموذج داخل .dropdown-menu
                        كان يخرج GET في المتصفح (405) رغم صحّة الـ markup.
                    --}}
                    @if ($payroll->status === 'draft')
                        <li>
                            <button type="button" class="dropdown-item"
                                    data-post-url="{{ route('admin.payrolls.calculate', $payroll->id) }}"
                                    data-post-confirm="احتساب كشف <strong>{{ $payroll->payroll_code }}</strong>؟"
                                    data-post-title="احتساب الكشف"
                                    data-post-type="info"
                                    data-post-btn="احتساب">
                                <i class="ri-calculator-line text-success me-2"></i>حساب
                            </button>
                        </li>
                    @endif

                    @if ($payroll->status === 'calculated')
                        <li>
                            <button type="button" class="dropdown-item"
                                    data-post-url="{{ route('admin.payrolls.approve', $payroll->id) }}"
                                    data-post-confirm="الموافقة على كشف <strong>{{ $payroll->payroll_code }}</strong> بصافي {{ number_format($payroll->net_salary, 2) }}؟"
                                    data-post-title="الموافقة على الكشف"
                                    data-post-type="warning"
                                    data-post-btn="موافقة">
                                <i class="ri-shield-check-line text-warning me-2"></i>موافقة
                            </button>
                        </li>
                    @endif

                    @can('payroll-show')
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.payrolls.payslip', $payroll->id) }}" target="_blank" rel="noopener">
                                <i class="ri-file-text-line text-secondary me-2"></i>كشف راتب
                            </a>
                        </li>
                        @if (in_array($payroll->status, ['calculated', 'approved', 'paid'], true))
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.payrolls.payslip.pdf', $payroll->id) }}" target="_blank" rel="noopener">
                                    <i class="ri-file-pdf-line text-danger me-2"></i>تحميل PDF
                                </a>
                            </li>
                        @endif
                    @endcan
                </ul>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="9">
            <div class="admin-empty-state">
                <i class="ri-file-paper-2-line"></i>
                لا توجد كشوف رواتب
            </div>
        </td>
    </tr>
@endforelse
