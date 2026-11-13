<!DOCTYPE html>
<html>
<head>
    <title>@yield('title', 'Quán Mới Quản Trị')</title>
</head>
<body>
    <div id="header-page-title" class="hidden">@yield('page-title')</div>
    <div id="admin-main-content">
        @yield('content')
    </div>
    @stack('scripts')
</body>
</html>
