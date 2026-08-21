@if ($countries->total() > 0)
    عرض {{ $countries->firstItem() }} إلى {{ $countries->lastItem() }} من {{ $countries->total() }} نتيجة
@else
    لا توجد نتائج
@endif
