<header class="main-nav">
    <div class="sidebar-user text-center">
        <h6 class="mt-3 f-14 f-w-600">{{ Auth::user()->name }}</h6>
        <p class="mb-0 font-roboto">
            @if(Auth::user()->role === 'admin')
                Administrator
            @elseif(Auth::user()->role === 'warga')
                Warga
            @else
                User
            @endif
        </p>
    </div>
    <nav>
        <div class="main-navbar">
            <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
            <div id="mainnav">
                <ul class="nav-menu custom-scrollbar">
                    <li class="back-btn">
                        <div class="mobile-back text-end">
                            <span>Back</span>
                            <i class="fa fa-angle-right ps-2" aria-hidden="true"></i>
                        </div>
                    </li>

                    <!-- Dashboard -->


                    <!-- Projects (Visible to all roles) -->
                    <li>
                        <a class="nav-link" href="{{ route('projects.index') }}">
                            <i data-feather="briefcase"></i><span>Projects</span>
                        </a>
                    </li>

                    <!-- Reports (Visible to all roles) -->
                    <li>
                        <a class="nav-link" href="{{ route('reports.index') }}">
                            <i data-feather="file-text"></i><span>Reports</span>
                        </a>
                    </li>

                    <!-- Discussions (Visible to all roles) -->
                    <li>
                        <a class="nav-link" href="/">
                            <i data-feather="message-square"></i><span>Discussions</span>
                        </a>
                    </li>

                    <!-- Admin-specific menu -->
                    @if(Auth::user()->role === 'admin')
                        <li>
                            <a class="nav-link" href="user.index">
                                <i data-feather="user"></i><span>User</span>
                            </a>
                        </li>
                        <li>
                            <a class="nav-link" href="{{ route('notifications.index') }}">
                                <i data-feather="bell"></i><span>Notifications</span>
                            </a>
                        </li>
                    @endif

                </ul>
            </div>
            <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
        </div>
    </nav>
</header>
