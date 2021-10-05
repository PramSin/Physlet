{{--language = HTML--}}
    <!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="utf-8">
    <title>测试页面</title>
    <link rel="stylesheet" href="/css/bootstrap.css">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/font-awesome.css">
    <script src="/js/jquery-3.6.0.js"></script>
    <script src="/js/bootstrap.js"></script>
</head>

<body>
<div id="header">
    <nav class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="#">Physlet API Test Page</a>
            </div>
            <div>
                <ul class="nav navbar-nav">
                    <li><a href="http://1.15.82.120:9090/" target="_blank">Home</a></li>
                    <!--                    <li><a href="tutorial.html" target="_blank">Tutorial</a></li>-->
                    <li><a href="http://phylab.fudan.edu.cn/doku.php" target="_blank">Phylab</a></li>
                </ul>
            </div>
        </div>
    </nav>
</div>
<div id="body" class="container" style="padding-left: 0; padding-top: 2px">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <form method="post" action="{{route('physlet.register')}}">
                    @csrf
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" class="form-control" name="username" placeholder="username">
                    </div>
                    <div class="form-group">
                        <label>Email address</label>
                        <input type="email" class="form-control" name="email" placeholder="Email">
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" class="form-control" name="password" placeholder="Password">
                    </div>
                    <button type="submit" class="btn btn-default">Register</button>
                </form>
            </div>
            @if(Auth::check())
                <div class="col-md-4">
                    <a href="{{route('physlet.logout')}}" class="btn btn-default">Logout</a>
                </div>
            @else
                <div class="col-md-4">
                    <form method="post" action="{{route('physlet.login')}}">
                        @csrf
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" class="form-control" name="emailOrUsername"
                                   placeholder="email or username">
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" name="password" placeholder="Password">
                        </div>
                        <button type="submit" class="btn btn-default">Login</button>
                    </form>
                </div>
            @endif
            <div class="col-md-4">
                <form method="post" action="{{route('physlet.changePassword')}}">
                    @csrf
                    <div class="form-group">
                        <label>Original Password</label>
                        <input type="password" class="form-control" name="ori_password" placeholder="Original Password">
                    </div>
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" class="form-control" name="password" placeholder="Password">
                    </div>
                    <button type="submit" class="btn btn-default">Change Password</button>
                </form>
            </div>
        </div>
        <div class="clearfix" style="margin-bottom: 40px;"></div>
        <div class="row">
        </div>
    </div>
</div>
<div id="footer">
    <nav class="navbar navbar-default navbar-fixed-bottom">
        <div class="navbar-header">
            <a class="navbar-text" href="#" style="user-select: none">加油努力！</a>
        </div>
        <div id="info" class="navbar-text" style="user-select: none"></div>
    </nav>
</div>

</body>

</html>
