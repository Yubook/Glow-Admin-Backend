<header id="page-topbar">
    <div class="navbar-header">

        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box" style="background: #FCDFD7 !important;">
                <a href="#" class="logo logo-dark mt-2">
                    <span class="logo-sm">
                        <img src="{{asset('images/logo-glow.png')}}" alt="glow-logo" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{asset('images/Glow.png')}}" alt="glow-logo" width="35%" height="35%">
                    </span>
                </a>

                <a href="#" class="logo logo-light mt-2">
                    <span class="logo-sm">
                        <img src="{{asset('images/logo-glow.png')}}" alt="glow-logo" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{asset('images/Glow.png')}}" alt="glow-logo" width="35%" height="35%">
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect" id="vertical-menu-btn">
                <i class="fa fa-fw fa-bars"></i>
            </button>
        </div>

        <div class="d-flex">
            <!--  <div class="d-inline-block" data-toggle="tooltip" title="Language">
                <button class="btn waves-effect header-item waves-light" data-toggle="modal" data-target="#change_language">
                    <i class="fas fa-language font-size-22"></i>
                </button>
            </div> -->

            <div class="dropdown d-inline-block d-lg-none ml-2">
                <button type="button" class="btn header-item noti-icon waves-effect" id="page-header-search-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="mdi mdi-magnify"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right p-0" aria-labelledby="page-header-search-dropdown">

                    <form class="p-3">
                        <div class="form-group m-0">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search ..." aria-label="Recipient's username">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="dropdown d-none d-lg-inline-block ml-1">
                <button type="button" class="btn header-item noti-icon waves-effect" data-toggle="fullscreen">
                    <i class="bx bx-fullscreen"></i>
                </button>
            </div>

            <div class="dropdown d-inline-block">

                <!--   <button type="button" class="btn header-item noti-icon waves-effect" id="page-header-notifications-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="bx bx-bell bx-tada"></i>
                    <span class="badge badge-primary badge-pill"></span>
                </button> -->

                <!--  <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right p-0" aria-labelledby="page-header-notifications-dropdown">
                    <div class="p-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="m-0">Notification</h6>
                            </div>
                            <div class="col-auto">
                                <a href="#"><button type="button" class="btn btn-link">Clear all</button></a>
                            </div>
                        </div>
                    </div>
                    <div data-simplebar style="max-height: 230px;">
                    </div>
                </div> -->
            </div>

            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="rounded-circle header-profile-user" src="{{ asset(Storage::url(Auth::user()->profile)) ?? '' }}" alt="Header Avatar">
                    <span class="d-none d-xl-inline-block ml-1"> {{ Auth::user()->name }}</span>
                    <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                    <!-- item-->
                    <!--  <a class="dropdown-item" href="#"><i class="bx bx-user font-size-16 align-middle mr-1"></i>View Profile</a> -->
                    <a class="dropdown-item" href="{{route('adminProfile.edit',Auth::user()->id)}}"><i class="bx bx-pencil font-size-16 align-middle mr-1"></i>Edit Profile</a>
                    <div class="dropdown-divider"></div>

                    <a class="dropdown-item text-danger" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();"><i class="bx bx-power-off font-size-16 align-middle mr-1 text-danger"></i>
                        {{ __('Logout') }}
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>