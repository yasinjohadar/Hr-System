<div class="admin-table-footer">
    <div class="admin-table-meta">
        @if ($heads->total() > 0)
            عرض {{ $heads->firstItem() }} إلى {{ $heads->lastItem() }} من {{ $heads->total() }} نتيجة
        @else
            لا توجد نتائج
        @endif
    </div>
    <div class="admin-pagination">
        {{ $heads->withQueryString()->links() }}
    </div>
</div>
