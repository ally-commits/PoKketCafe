<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Excel Bakers') }}</title>

    <!-- Scripts -->
   
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/style.css') }}">
    <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
    @yield('css')
</head>
<body>
<div class="page-wrapper chiller-theme toggled">
    <a id="show-sidebar" class="btn btn-sm btn-dark" href="#">
        <i class="fas fa-bars"></i>
    </a>
    <nav id="sidebar" class="sidebar-wrapper">
        <div class="sidebar-content">
            <div class="sidebar-brand">
                <a href="#">User Dashboard</a>
                <div id="close-sidebar">
                    <i class="fas fa-times"></i>
                </div>
            </div>
            <div class="sidebar-header">
                <div class="user-pic">
                <img class="img-responsive img-rounded" src="https://raw.githubusercontent.com/azouaoui-med/pro-sidebar-template/gh-pages/src/img/user.jpg"
                    alt="User picture">
                </div>
                <div class="user-info">
                    <span class="user-name"> {{ Auth::user()->name }} 
                </span> 
                <span class="user-status">
                    <i class="fa fa-circle"></i>
                    <span>Online</span>
                </span>
                </div>
            </div> 
            <div class="sidebar-menu">
                <ul> 
                    <li class="sidebar-dropdown">
                        <a href="/profile">
                            <i class="fa fa-tachometer-alt"></i>
                            <span>Profile</span> 
                        </a> 
                    </li> 
                    <li class="sidebar-dropdown">
                        <a href="/edit-profile">
                            <i class="fa fa-tachometer-alt"></i>
                            <span>Edit Profile</span> 
                        </a> 
                    </li> 
                    <li class="sidebar-dropdown">
                        <a href="/add-address">
                            <i class="fa fa-tachometer-alt"></i>
                            <span>Add Address</span> 
                        </a> 
                    </li> 
                    <li class="sidebar-dropdown">
                        <a href="/admin/add-product">
                            <i class="fa fa-tachometer-alt"></i>
                            <span>Your Orders</span> 
                        </a> 
                    </li> 
                </ul>
            </div> 
        </div> 
    </nav> 
    <main class="page-content"> 
        @yield('content') 
    </main> 
</div> 
    
    <script src="{{ asset('js/jquery.js') }}"></script>
    <script src="{{ asset('js/popper.js') }}"></script>
    <script src="{{ asset('js/bootstrap.js') }}"></script>
    @yield('js')
</body>
</html>