<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$title ?? ''}}</title>
    <link style="text/css" rel="stylesheet" href="{{ url(mix('/css/app.css', 'public')) }}">
    <script src="{{ url(mix('/js/app.js', 'public')) }}" type="text/javascript"></script>
    @yield('after_css')
</head>
<body>

@include('layout.header')
@yield('content')
@include('layout.footer')
@yield('after_script')
<script>
    var app_path = '{{ url() }}';
</script>
</body>
</html>