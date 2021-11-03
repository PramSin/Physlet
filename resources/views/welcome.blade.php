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
                <form method="post" action="{{route('physlet.register')}}" target="nm_iframe">
                    @csrf
                    <div class="form-group">
                        <label for="register-username">Username</label>
                        <input type="text" id="register-username" class="form-control" name="username"
                               placeholder="username">
                    </div>
                    <div class="form-group">
                        <label for="register-email">Email address</label>
                        <input type="email" id="register-email" class="form-control" name="email" placeholder="Email">
                    </div>
                    <div class="form-group">
                        <label for="register-password">Password</label>
                        <input type="password" id="register-password" class="form-control" name="password"
                               placeholder="Password">
                    </div>
                    <button type="submit" class="btn btn-default">Register</button>
                </form>
            </div>
            @if(Auth::check())
                <div class="col-md-4">
                    <a href="{{route('physlet.logout')}}" class="btn btn-default">Logout</a>
                </div>
                <div class="col-md-4">
                    <form method="post" action="{{route('physlet.changePassword')}}" target="nm_iframe">
                        @csrf
                        <div class="form-group">
                            <label for="change-ori-password">Original Password</label>
                            <input type="password" id="change-ori-password" class="form-control" name="ori_password"
                                   placeholder="Original Password">
                        </div>
                        <div class="form-group">
                            <label for="change-password">New Password</label>
                            <input type="password" id="change-password" class="form-control" name="password"
                                   placeholder="Password">
                        </div>
                        <button type="submit" class="btn btn-default">Change Password</button>
                    </form>
                </div>
            @else
                <div class="col-md-4">
                    <form method="post" action="{{route('physlet.login')}}" target="nm_iframe">
                        @csrf
                        <div class="form-group">
                            <label for="login-name">Username</label>
                            <input id="login-name" type="text" class="form-control" name="emailOrUsername"
                                   placeholder="email or username">
                        </div>
                        <div class="form-group">
                            <label for="login-password">Password</label>
                            <input id="login-password" type="password" class="form-control" name="password"
                                   placeholder="Password">
                        </div>
                        <button type="submit" class="btn btn-default">Login</button>
                    </form>
                </div>
            @endif
        </div>
        <div class="clearfix" style="margin-bottom: 40px;"></div>
        <div class="row">
            @if(Auth::check())
                {{--                @php--}}
                {{--                    $simulationController = new \App\Http\Controllers\SimulationController;--}}
                {{--                    $categories = $simulationController->getCategories()['data'];--}}
                {{--                @endphp--}}
                <div class="col-md-4">
                    <form method="post" action="{{ route('physlet.uploadSimulation') }}" enctype="multipart/form-data"
                    target="nm_iframe">
                        @csrf
                        <div class="form-group">
                            <label for="simulation-name">Simulation Name</label>
                            <input id="simulation-name" type="text" class="form-control" name="name"
                                   placeholder="name of the your simulation">
                        </div>
                        <div class="form-group">
                            <label for="simulation-category">Category</label>
                            <select name="category" id="simulation-category" class="form-control">
                                @foreach([1,2,3,4] as $key => $category)
                                    <option value={{ $category }}>{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="simulation-synopsis">Synopsis</label>
                            <input id="simulation-synopsis" type="text" class="form-control" name="synopsis"
                                   placeholder="synopsis for the your simulation">
                        </div>
                        <div class="form-group">
                            <label for="simulation-access">Access</label>
                            <select name="access" id="simulation-access" class="form-control">
                                <option value=0>Public</option>
                                <option value=1>Private</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="simulation-package">Package</label>
                            <input name="package" id="simulation-package" type="file" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-default">Submit</button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>

<iframe id="id_iframe" name="nm_iframe" style=""></iframe>

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
