<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Call Logs</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        .pointer {
            cursor: pointer
        }
        [v-cloak] {
            display: none;
        }
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top border-bottom">
            <a class="navbar-brand" href="https://www.tripclues.in/leadAPI/public/callLogs"><img src="vuejs/logo.png" height="40" alt=""></a>
            <div class="navbar-collapse">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <router-link to="/" class="nav-link">Dashboard</router-link>
                    </li>
                    <li class="nav-item">
                        <router-link to="/logs" class="nav-link">Call Logs</router-link>
                    </li>
                </ul>
                <search></search>
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
            </div>
        </nav>
        <router-view style="margin-top: 66px"></router-view>
    </div>

    <script src="vuejs/app.js"></script>
</body>
</html>