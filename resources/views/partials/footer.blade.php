{{--
    فوتر النظام — مشترك بين بوابة الإدارة وبوابة الموظف.

    السنة تُحسب على الخادم بـ now()->year (المنطقة الزمنية من config/app.php،
    وهي UTC حالياً) فتظهر صحيحة حتى لو كان JavaScript معطّلاً أو فشل التحميل.

    <span id="year"> المخفي إجباري ولا يُحذف: assets/js/custom.js في السطر
    314 يفعل document.getElementById("year").innerHTML = … بدون فحص null،
    فحذف العنصر يرمي TypeError ويُوقف تنفيذ بقية ذلك الملف. إبقاؤه مخفياً
    يُرضي القالب ويحفظ للسنة المعروضة قيمتها من الخادم لا من ساعة الزائر.
--}}
<!-- Footer Start -->
<footer class="footer mt-auto py-3 bg-white text-center hr-footer">
    <div class="container">
        <span class="hr-footer__text">
            جميع الحقوق محفوظة &copy; {{ now()->year }}
            <span class="hr-footer__sep" aria-hidden="true">—</span>
            تصميم وبرمجة
            <span class="hr-footer__org">مؤسسة كلاودسوفت</span>
            بواسطة
            <span class="hr-footer__author">ياسين جوخدار</span>
        </span>

        {{-- مطلوب لـ custom.js — انظر التعليق أعلى الملف --}}
        <span id="year" class="d-none" aria-hidden="true"></span>
    </div>
</footer>
<!-- Footer End -->
