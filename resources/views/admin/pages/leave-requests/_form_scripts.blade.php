<script>
(function () {
    const startEl = document.getElementById('start_date');
    const endEl = document.getElementById('end_date');
    const preview = document.getElementById('leave-days-preview');
    if (!startEl || !endEl || !preview) return;

    function updateDays() {
        const start = startEl.value;
        const end = endEl.value;
        if (!start || !end) {
            preview.classList.add('is-hidden');
            preview.textContent = '';
            return;
        }
        const s = new Date(start);
        const e = new Date(end);
        if (e < s) {
            preview.classList.remove('is-hidden');
            preview.textContent = 'تاريخ النهاية يجب أن يكون بعد البداية';
            return;
        }
        const diff = Math.ceil(Math.abs(e - s) / (1000 * 60 * 60 * 24)) + 1;
        preview.classList.remove('is-hidden');
        preview.textContent = diff + ' يوم';
    }

    startEl.addEventListener('change', updateDays);
    endEl.addEventListener('change', updateDays);
    updateDays();
})();
</script>
