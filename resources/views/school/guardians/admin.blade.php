<li class="nav-item">
    <a class="nav-link text-white d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#guardiansMenu" role="button">
        <span><i class="fas fa-user-friends ms-2"></i> مدیریت اولیا</span>
        <i class="fas fa-chevron-down small"></i>
    </a>
    <div class="collapse" id="guardiansMenu">
        <ul class="nav flex-column ps-3">
            <li class="nav-item"><a href="{{ route('school.guardians.index') }}" class="nav-link text-white-50 small py-1">لیست اولیا</a></li>
            <li class="nav-item"><a href="{{ route('school.guardians.create') }}" class="nav-link text-white-50 small py-1">ثبت ولی جدید</a></li>
            <li class="nav-item"><a href="{{ route('school.guardians.index', ['financial' => 'debtor']) }}" class="nav-link text-white-50 small py-1">خانواده‌های بدهکار</a></li>
            <li class="nav-item"><a href="{{ route('school.guardians.index', ['financial' => 'discount']) }}" class="nav-link text-white-50 small py-1">خانواده‌های دارای تخفیف</a></li>
            <li class="nav-item"><a href="{{ route('school.guardians.index', ['financial' => 'free']) }}" class="nav-link text-white-50 small py-1">خانواده‌های یتیم</a></li>
            <li class="nav-item"><a href="{{ route('school.guardians.report') }}" class="nav-link text-white-50 small py-1">گزارش اولیا</a></li>
        </ul>
    </div>
</li>
