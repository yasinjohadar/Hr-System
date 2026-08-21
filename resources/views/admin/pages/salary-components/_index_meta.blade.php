@if ($components->total() > 0)
    عرض {{ $components->firstItem() }} إلى {{ $components->lastItem() }} من {{ $components->total() }} نتيجة
@else
    لا توجد نتائج
@endif
