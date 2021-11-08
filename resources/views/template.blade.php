<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title></title>
    <meta name="keywords" content="" />
    <meta name="description" content="" />

    <script type="application/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,400,600,700,900" rel="stylesheet" />
    <link href="/css/default.css" rel="stylesheet" />
    <link href="/css/fonts.css" rel="stylesheet" />
    <link href="https://bulma.io/css/bulma-docs.min.css?v=202004111236" rel="stylesheet">
    <link href={{asset('css/app.css')}} rel="stylesheet" />

    @yield('custom-head-section')

</head>
<body>
    <div id="header-wrapper">
        <div id="header" class="container">
            <div id="logo">
                <h1><a href="/">Point of Sale</a></h1>
            </div>
            <div id="menu">
                <ul>
                    <li class="{{ Request::is('/') ? 'current_page_item' : ''}}"><a href="/" accesskey="2"
                                                                                    title="">Home</a></li>
                    <li class="{{ Request::is('printing*') ? 'current_page_item' : ''}}"><a href="/printing/" accesskey="3"
                                                                                         title="">Printing</a></li>
                    <li class="{{ Request::is('finding*') ? 'current_page_item' : ''}}"><a href="/finding/" accesskey="4"
                                                                                         title="">Finding</a></li>
                    <li class="{{ Request::is('admin*') ? 'current_page_item' : ''}}"><a href="/admin" accesskey="5"
                                                                                         title="">Admin</a></li>
                </ul>
            </div>
        </div>
    </div>

    @yield('content')

    <div id="copyright" style="background-color: teal;">
        <p>&copy; {{ (new DateTime())->format('Y') }} Brian Etheridge</p>
    </div>

    <script type="application/javascript" src="/js/app.js?t=1"></script>

    @extends('common.js')

</body>
</html>
