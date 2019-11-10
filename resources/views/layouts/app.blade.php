<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/home') }}" tabindex="-1">
                    <img src="https://www.tripclues.in/leadAPI/public/vuejs/logo.png" alt="" width="40">
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}" tabindex="-1">{{ __('Login') }}</a>
                            </li>
                            <li class="nav-item">
                                @if (Route::has('register'))
                                    <a class="nav-link" href="{{ route('register') }}" tabindex="-1">{{ __('Register') }}</a>
                                @endif
                            </li>
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" tabindex="-1" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" tabindex="-1" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        {{csrf_field()}}
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @if ($errors->any())
                <div class="alert alert-danger m-3">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <ul class="m-0 p-1 list-unstyled">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (session('message'))
                <div class="alert alert-success m-3">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    {{ session('message') }}
                </div>
            @endif
            @if (session('error'))
            <div class="alert alert-danger m-3">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                {{ session('error') }}
            </div>
            @endif

            <div class="container-fluid">
                <div class="row">
                    <div class="col-2">
                        @auth
                        @php
                        $unidentified = \App\FormRequest::whereNull('name')->count();
                        $unmapped = \App\WebsiteMap::where('form_map_id', 0)->count();
                        @endphp
                        <div class="list-group">
                            <a tabindex="-1" href="{{route('formRequest')}}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">Requests @if($unidentified)<span class="badge badge-danger badge-pill">{{$unidentified}}</span>@endif</a>
                            <a tabindex="-1" href="{{route('table')}}" class="list-group-item list-group-item-action">CRM Tables</a>
                            <a tabindex="-1" href="{{route('website')}}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">Website @if($unmapped)<span class="badge badge-danger badge-pill">{{$unmapped}}</span>@endif</a>
                            @foreach(\App\FormMap::all() as $formmap)
                            @if(!count($formmap->FormRequestRoute))
                            <a tabindex="-1" href="{{route('requestRoute', ["map_id" => $formmap->id])}}" class="list-group-item list-group-item-action bg-warning">{{$formmap->FormRequest->name}} - {{$formmap->CrmTable->name}}</a>
                            @endif
                            @endforeach
                        </div>
                        @endauth
                    </div>
                    <div class="col-10">
                        @yield('content')
                    </div>
                </div>
            </div>
        </main>
    </div>
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>