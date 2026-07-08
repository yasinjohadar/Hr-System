<div class="admin-table-footer">
    <div class="admin-table-meta" id="users-table-meta">
        @if ($users->total() > 0)
            عرض {{ $users->firstItem() }} إلى {{ $users->lastItem() }} من {{ $users->total() }} نتيجة
        @else
            لا توجد نتائج
        @endif
    </div>
    <div class="admin-pagination" id="users-pagination">
        {{ $users->withQueryString()->links() }}
    </div>
</div>

<div id="users-modals">
    @foreach ($users as $user)
        @include('admin.pages.users.change_password')
    @endforeach
</div>
