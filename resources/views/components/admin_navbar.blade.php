<!--start top header-->
<header class="top-header">
    <nav class="navbar navbar-expand gap-3">
    <div class="mobile-toggle-icon fs-3">
        <i class="bi bi-list"></i>
    </div>
    <div class="top-navbar-right ms-auto">
        <div class="dropdown dropdown-user-setting">
        <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
            <div class="user-setting d-flex align-items-center gap-3">
            <img src="{{asset(check_file(auth('admin')->user()->image,'user'))}}" class="user-img" alt="user avatar">
            <div class="d-none d-sm-block">
                <p class="user-name mb-0">{{auth('admin')->user()->name}}</p>
                @if(auth('admin')->user()->user_type == 'normal')
                    <small class="text-muted m-0 p-0" style=" font-weight: initial; font-size: 16px; ">{{auth('admin')->user()->role_name}}</small>
                @endif
            </div>

            </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
            <li>
                <a class="dropdown-item" href="{{route('admin.profile.edit')}}">
                    <div class="d-flex align-items-center">
                    <div class=""><i class="bi bi-person-fill"></i></div>
                    <div class="ms-3"><span>Profile</span></div>
                    </div>
                </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item" href="{{ route('admin.logout') }}" onclick="logout(event)">
                    <div class="d-flex align-items-center">
                    <div class=""><i class="bi bi-lock-fill"></i></div>
                    <div class="ms-3"><span>Logout</span></div>
                    </div>
                </a>
            </li>
        </ul>
        </div>
    </nav>
</header>
   <!--end top header-->
