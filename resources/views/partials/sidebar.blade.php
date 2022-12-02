<div class="vertical-menu" style="background-color: #FCDFD7 !important;">
    <div data-simplebar class="h-100">
        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <!-- Admin show menu start -->

            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title">Menu</li>
                <li class="{{ request()->is('dashboard') ? 'mm-active' : '' }}">
                    <a href="{{ route('home.dashboard') }}" class="{{ request()->is('dashboard') || request()->is('dashboard/*') ? 'active' : '' }} waves-effect">
                        <i class="bx bx-home-circle"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="{{ request()->is('barbers') || request()->is('barbers/*') ? 'mm-active' : '' }}" class="waves-effect">
                    <a href="{{ route('barbers.index') }}" class="{{ request()->is('barbers') || request()->is('barbers/*') ? 'active' : '' }} waves-effect">
                        <i class="fas fa-car"></i>
                        <span>Professional</span>
                    </a>
                </li>
                <li class="{{ request()->is('users') || request()->is('users/*') ? 'mm-active' : '' }}" class="waves-effect">
                    <a href="{{ route('users.index') }}" class="{{ request()->is('users') || request()->is('users/*') ? 'active' : '' }} waves-effect">
                        <i class="fas fa-users"></i>
                        <span>User</span>
                    </a>
                </li>

                <!-- <li class="">
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bx-user"></i>
                        <span>User</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                    </ul>
                 </li> -->

                <li class="{{ request()->is('chat') || request()->is('chat/*') ? 'mm-active' : '' }}">
                    <a href="{{ route('chat.view') }}" class="custom_menu_chat waves-effect {{ request()->is('chat') || request()->is('chat/*') ? 'active' : '' }}">
                        <i class="bx bx-chat"></i>
                        <span>Chat</span>
                    </a>
                </li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bx-cog"></i>
                        <span>Setting</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li class="{{ request()->is('services') || request()->is('services/*') ? 'mm-active' : '' }}" class="waves-effect">
                            <a href="{{ route('services.index') }}" class="{{ request()->is('services') || request()->is('services/*') ? 'active' : '' }} waves-effect">
                                <i class="fa fa-wrench"></i>
                                <span>Service</span>
                            </a>
                        </li>
                        <li class="{{ request()->is('reasons') || request()->is('reasons/*') ? 'mm-active' : '' }}" class="waves-effect">
                            <a href="{{ route('reasons.index') }}" class="{{ request()->is('reasons') || request()->is('reasons/*') ? 'active' : '' }} waves-effect">
                                <i class="fa fa-comments" aria-hidden="true"></i>
                                <span>Reason</span>
                            </a>
                        </li>
                        <li class="{{ request()->is('categories') || request()->is('categories/*') ? 'mm-active' : '' }}" class="waves-effect">
                            <a href="{{ route('categories.index') }}" class="{{ request()->is('categories') || request()->is('categories/*') ? 'active' : '' }} waves-effect">
                                <i class="fa fa-object-ungroup" aria-hidden="true"></i>
                                <span>Category</span>
                            </a>
                        </li>
                        <li class="{{ request()->is('subcategories') || request()->is('subcategories/*') ? 'mm-active' : '' }}" class="waves-effect">
                            <a href="{{ route('subcategories.index') }}" class="{{ request()->is('subcategories') || request()->is('subcategories/*') ? 'active' : '' }} waves-effect">
                                <i class="fa fa-object-group" aria-hidden="true"></i>
                                <span>SubCategory</span>
                            </a>
                        </li>
                        <li class="{{ request()->is('timings') || request()->is('timings/*') ? 'mm-active' : '' }}" class="waves-effect">
                            <a href="{{ route('timings.index') }}" class="{{ request()->is('timings') || request()->is('timings/*') ? 'active' : '' }} waves-effect">
                                <i class="fas fa-calendar-plus"></i>
                                <span>Timing</span>
                            </a>
                        </li>
                        <li class="{{ request()->is('cities') || request()->is('cities/*') ? 'mm-active' : '' }}" class="waves-effect">
                            <a href="{{ route('cities.index') }}" class="{{ request()->is('cities') || request()->is('cities/*') ? 'active' : '' }} waves-effect">
                                <i class="fa fa-location-arrow"></i>
                                <span>City</span>
                            </a>
                        </li>
                        <li class="{{ request()->is('states') || request()->is('states/*') ? 'mm-active' : '' }}" class="waves-effect">
                            <a href="{{ route('states.index') }}" class="{{ request()->is('states') || request()->is('states/*') ? 'active' : '' }} waves-effect">
                                <i class="fa fa-map-marker"></i>
                                <span>State</span>
                            </a>
                        </li>
                        <li class="{{ request()->is('terms') || request()->is('terms/*') ? 'mm-active' : '' }}" class="waves-effect">
                            <a href="{{ route('terms.index') }}" class="{{ request()->is('terms') || request()->is('terms/*') ? 'active' : '' }} waves-effect">
                                <i class="fa fa-gavel" aria-hidden="true"></i>
                                <span>Term & Policy</span>
                            </a>
                        </li>

                    </ul>
                </li>
            </ul>
            <!-- Admin show menu end -->
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End