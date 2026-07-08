<div class="admin-table-footer">
    <div class="admin-table-meta" id="roles-table-meta">
        @if ($roles->total() > 0)
            عرض {{ $roles->firstItem() }} إلى {{ $roles->lastItem() }} من {{ $roles->total() }} نتيجة
        @else
            لا توجد نتائج
        @endif
    </div>
    <div class="admin-pagination" id="roles-pagination">
        {{ $roles->withQueryString()->links() }}
    </div>
</div>
