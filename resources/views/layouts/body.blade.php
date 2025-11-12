<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">

        @include('layouts.navbar')
        @include('layouts.sidebar')
        <!--begin::App Main-->
        <main class="app-main">
            @include('layouts.breadcrumb')
            @include('layouts.content')

        </main>
        <!--end::App Main-->
        @include('layouts.footer')
    </div>

</body>
