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
        <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
            <a class="navbar-brand" href="https://www.tripclues.in/leadAPI/public/callLogs"><img src="https://www.tripclues.in/images/tripclues-logo.jpg" height="40" alt=""></a>
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
            </div>
        </nav>
        <keep-alive>
            <router-view style="margin-top: 66px"></router-view>
        </keep-alive>
    </div>

    <script src="vuejs/app.js"></script>
</body>
</html>