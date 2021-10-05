<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Arrakis</title>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="stylesheet" href="{{ mix('/css/bootstrap.css') }}">
</head>
<body>
<div id="app">
    <arrakis>载入中...</arrakis>
</div>
<script type="text/javascript">
    const arrakis_authenticated = {{ Auth::user() ? 'true' : 'false'}};
    const asset_version = '{{ config('asset_version') }}';
</script>
<script type="text/javascript" src="{{ mix('/js/manifest.js') }}"></script>
<script type="text/javascript" src="{{ mix('/js/vendor.js') }}"></script>
<script type="text/javascript" src="{{ mix('/js/main.js') }}"></script>
@if (0 && env('APP_ENV', 'local') == 'local')
    <script type="text/javascript" src="//cdn.staticfile.org/vConsole/3.3.0/vconsole.min.js"></script>
    <script>
        var vConsole = new VConsole();
    </script>
@endif
</body>
</html>
