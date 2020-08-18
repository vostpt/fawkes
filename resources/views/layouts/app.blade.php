<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{mix('css/app.css')}}">
    <link rel="stylesheet" href="{{mix('css/cover.css')}}">
    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }
    </style>
    <title>VOSTPT - Fawkes</title>
</head>

<body class="text-center">
    <div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
        <header class="masthead mb-auto">
            <div class="inner">
                <h3 class="masthead-brand"><img src="/img/logo.png" style="width:5vw;"/></h3>
                <nav class="nav nav-masthead justify-content-center">
                    <a class="nav-link" href="/">Inicio</a>
                    <a class="nav-link" href="/info">Info & API</a>
                </nav>
            </div>
        </header>

        @yield('content')

        <footer class="mastfoot mt-auto">
            <div class="inner">
                <p>Feito com ❤ por <a href="https://vost.pt/">VOST Portugal</a>.</p>
            </div>
        </footer>
    </div>

    <script src="{{mix('js/app.js')}}"></script>
    @yield('scripts')
</body>

</html>