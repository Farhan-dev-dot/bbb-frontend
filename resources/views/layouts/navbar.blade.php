        <!--begin::Header-->
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <!-- Navbar kiri -->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                            <i class="bi bi-list"></i>
                        </a>
                    </li>
                </ul>

                <!-- Navbar kanan -->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a href="{{ route('home.logout') }}" class="btn btn-danger">
                            Logout <i class="bi bi-arrow-bar-right"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!--end::Header-->
