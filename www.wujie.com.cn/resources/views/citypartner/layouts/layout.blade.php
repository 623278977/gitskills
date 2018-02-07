<!DOCTYPE html >
<html>
<head lang="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    @yield('title')
    @yield('styles')
    <script type="text/javascript">
        var uploadUrl = "{{url('citypartner/upload/index')}}";
        var url_prex="/citypartner/";
    </script>
</head>
<body>
@include('citypartner.layouts.partials.header')
@yield('content')
@include('citypartner.layouts.partials.footer')
@yield('scripts')
</body>
</html>