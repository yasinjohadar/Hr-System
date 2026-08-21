@if ($taxSettings->total() > 0)
    عرض {{ $taxSettings->firstItem() }} إلى {{ $taxSettings->lastItem() }} من {{ $taxSettings->total() }} نتيجة
@else
    لا توجد نتائج
@endif
