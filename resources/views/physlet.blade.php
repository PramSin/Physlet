<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Physlet</title>
    <style>
        html,
        body {
            height: 100%;
        }

        body {
            background: #FFFFFF;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        #app {
            position: relative;
            width: 600px;
            height: 36px;
            user-select: none;
        }

        #app div {
            position: absolute;
            width: 20px;
            height: 36px;
            font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif, Helvetica, sans-serif;
            font-size: 30px;
            opacity: 0;
            transform: rotate(180deg);
            animation: move 2s linear infinite;
            color: #337ab7;
        }

        #app div:nth-child(2) {
            animation-delay: .2s;
        }

        #app div:nth-child(3) {
            animation-delay: .4s;
        }

        #app div:nth-child(4) {
            animation-delay: .6s;
        }

        #app div:nth-child(5) {
            animation-delay: .8s;
        }

        #app div:nth-child(6) {
            animation-delay: 1.0s;
        }

        #app div:nth-child(7) {
            animation-delay: 1.2s;
        }

        @keyframes move {
            0% {
                left: 0;
                opacity: 0;
            }
            35% {
                left: 40%;
                transform: rotate(0deg);
                opacity: 1;
            }
            65% {
                left: 60%;
                transform: rotate(0deg);
                opacity: 1;
            }
            100% {
                left: 100%;
                transform: rotate(-180deg);
                opacity: 0;
            }
        }
    </style>
    <link rel="stylesheet" href="{{ mix('/css/bootstrap.css') }}">
</head>
<body>
<div id="app">
    <physlet id="app">
        <div>G</div>
        <div>N</div>
        <div>I</div>
        <div>D</div>
        <div>A</div>
        <div>O</div>
        <div>L</div>
    </physlet>
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
