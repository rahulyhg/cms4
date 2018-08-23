<!DOCTYPE html>
<!--[if lt IE 7]>
<html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>
<html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>
<html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <title>{{ page_title()->getTitle() }}</title>

        <meta name='robots' content='noindex,follow' />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="icon shortcut" href="{{ url('/images/favicon.png') }}">

        {!! Assets::renderHeader() !!}

        <script src="{{ url('vendor/core/media/packages/dropzone/dropzone.js') }}"></script>

        @if (array_key_exists($active_theme, $themes))
            {!! Html::style($themes[$active_theme] . '?v=' . time()) !!}
        @endif

        @yield('head')
    </head>

    <body class="@yield('body-class')" id="@yield('body-id', 'module')">

        <!--[if lt IE 7]>
        <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        @yield('page')


        @include('core.base::elements.common')

        {!! Assets::renderFooter() !!}

        @yield('javascript')

        @stack('footer')

    </body>

</html>
