<!DOCTYPE html>
<html lang="en">
@include('includes.dashboard.head')
@include('sweetalert::alert')

<body>
    @include('includes.dashboard.navbar')
    <div id="layoutSidenav">
        @include('includes.dashboard.sidebar')
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    @yield('content')
                </div>
            </main>
            @include('includes.dashboard.footer')
        </div>
    </div>
    @include('includes.dashboard.script')
</body>

</html>
