<html>
    <head>
        <link href="{{ asset('css/app.css')}}" rel="stylesheet">
        <title>Página de Produtos</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <style>
            body{
                padding: 20px;
            }

            .navbar{
                margin-bottom:20px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            @component('component_navbar', ["current"=> "$current"]) <!-- Esse component contém a navbar. Tem que colocar antes do conteúdo principal-->                                    
            @endcomponent
            <main role="main">
                @hasSection('body')
                    @yield('body')        
                @endif
            </main>
        </div>
    </body>
    <script src="{{ asset('js/app.js')}}" type="text/javascript"></script>

    @hasSection('javascript')
        @yield('javascript')
    @endif
</html>