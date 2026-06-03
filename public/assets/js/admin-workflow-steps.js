(function () {
    const editor = document.querySelector('[data-workflow-steps-editor]');
    if (!editor) return;

    const list = editor.querySelector('[data-steps-list]');
    const templateEl = document.getElementById('workflow-step-row-template');
    const emptyHint = editor.querySelector('[data-empty-hint]');
    const defaultJson = document.getElementById('workflow-default-template-json');

    function getTemplateHtml() {
        if (!templateEl) return '';
        return templateEl.innerHTML.trim();
    }

    function reindexSteps() {
        const cards = list.querySelectorAll('[data-step-card]');
        cards.forEach((card, index) => {
            card.dataset.index = String(index);
            const label = card.querySelector('[data-step-order-label]');
            if (label) label.textContent = 'الخطوة ' + (index + 1);

            card.querySelectorAll('[name^="steps["]').forEach((input) => {
                const name = input.getAttribute('name');
                if (!name) return;
                input.setAttribute(
                    'name',
                    name.replace(/steps\[[^\]]+\]/, 'steps[' + index + ']')
                );
            });

            card.querySelectorAll('[id^="step_required_"], [id^="step_reject_"]').forEach((input) => {
                const id = input.getAttribute('id');
                if (!id) return;
                const prefix = id.startsWith('step_required_') ? 'step_required_' : 'step_reject_';
                input.setAttribute('id', prefix + index);
                const label = card.querySelector('label[for="' + id + '"]');
                if (label) label.setAttribute('for', prefix + index);
            });
        });

        if (emptyHint) {
            emptyHint.style.display = cards.length === 0 ? '' : 'none';
        }
    }

    function toggleApproverFields(card) {
        const typeSelect = card.querySelector('[data-approver-type]');
        if (!typeSelect) return;

        const type = typeSelect.value;
        const roleField = card.querySelector('[data-role-field]');
        const userField = card.querySelector('[data-user-field]');

        if (roleField) {
            roleField.style.display = type === 'role' ? '' : 'none';
            const roleSelect = roleField.querySelector('select');
            if (roleSelect) roleSelect.required = type === 'role';
        }
        if (userField) {
            userField.style.display = type === 'user' ? '' : 'none';
            const userSelect = userField.querySelector('select');
            if (userSelect) userSelect.required = type === 'user';
        }
    }

    function bindCard(card) {
        const typeSelect = card.querySelector('[data-approver-type]');
        if (typeSelect) {
            typeSelect.addEventListener('change', () => toggleApproverFields(card));
            toggleApproverFields(card);
        }

        const upBtn = card.querySelector('[data-move-up]');
        const downBtn = card.querySelector('[data-move-down]');
        const removeBtn = card.querySelector('[data-remove-step]');

        if (upBtn) {
            upBtn.addEventListener('click', () => {
                const prev = card.previousElementSibling;
                if (prev) {
                    list.insertBefore(card, prev);
                    reindexSteps();
                }
            });
        }

        if (downBtn) {
            downBtn.addEventListener('click', () => {
                const next = card.nextElementSibling;
                if (next) {
                    list.insertBefore(next, card);
                    reindexSteps();
                }
            });
        }

        if (removeBtn) {
            removeBtn.addEventListener('click', () => {
                if (list.querySelectorAll('[data-step-card]').length <= 1) {
                    alert('يجب أن يبقى على الأقل خطوة موافقة واحدة.');
                    return;
                }
                card.remove();
                reindexSteps();
            });
        }
    }

    function addStep(stepData) {
        let html = getTemplateHtml().replace(/__INDEX__/g, String(list.querySelectorAll('[data-step-card]').length));
        html = html.replace(/__ORDER__/g, String(list.querySelectorAll('[data-step-card]').length + 1));

        const wrapper = document.createElement('div');
        wrapper.innerHTML = html;
        const card = wrapper.firstElementChild;
        if (!card) return;

        list.appendChild(card);

        if (stepData) {
            const nameAr = card.querySelector('[name*="[name_ar]"]');
            if (nameAr) nameAr.value = stepData.name_ar || '';
            const typeSelect = card.querySelector('[data-approver-type]');
            if (typeSelect) typeSelect.value = stepData.approver_type || 'department_manager';
            const roleSelect = card.querySelector('[data-role-select]');
            if (roleSelect && stepData.role_id) roleSelect.value = String(stepData.role_id);
            const userSelect = card.querySelector('[data-user-select]');
            if (userSelect && stepData.approver_id) userSelect.value = String(stepData.approver_id);
            const req = card.querySelector('[name*="[is_required]"][type="checkbox"]');
            if (req) req.checked = stepData.is_required !== false && stepData.is_required !== '0';
            const rej = card.querySelector('[name*="[can_reject]"][type="checkbox"]');
            if (rej) rej.checked = stepData.can_reject !== false && stepData.can_reject !== '0';
        }

        bindCard(card);
        reindexSteps();
    }

    editor.querySelectorAll('[data-step-card]').forEach(bindCard);

    const addBtn = editor.querySelector('[data-add-step]');
    if (addBtn) {
        addBtn.addEventListener('click', () => addStep({
            name_ar: '',
            approver_type: 'department_manager',
            is_required: true,
            can_reject: true,
        }));
    }

    const loadTemplateBtn = editor.querySelector('[data-load-default-template]');
    if (loadTemplateBtn && defaultJson) {
        loadTemplateBtn.addEventListener('click', () => {
            if (!confirm('سيتم استبدال الخطوات الحالية بالقالب الافتراضي. متابعة؟')) {
                return;
            }
            let template = [];
            try {
                template = JSON.parse(defaultJson.textContent || '[]');
            } catch (e) {
                return;
            }
            list.innerHTML = '';
            template.forEach((step) => addStep(step));
        });
    }

    reindexSteps();
})();
