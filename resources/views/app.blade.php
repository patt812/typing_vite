<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="{{config('app.name', 'Laravel')}}は文章と出題を自由に設定できる無料のタイピングアプリです。過去の記録から様々な情報を分析したり、弱点を集中的に克服できます。">

        <meta property="og:title" content="{{config('app.name', 'Laravel')}}" />
        <meta property="og:description" content="{{config('app.name', 'Laravel')}}は文章と出題を自由に設定できる無料のタイピングアプリです。過去の記録から様々な情報を分析したり、弱点を集中的に克服できます。" />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="{{config('app.url')}}" />
        <meta property="og:site_name" content="{{config('app.name', 'Laravel')}}" />
        <meta property="og:locale" content="ja_JP"  />
        @if (file_exists(public_path(('/icons/logo.png'))))
            <meta property="og:image" content="{{ asset('/icons/logo.png') }}" />
        @endif

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap">
        @if (app()->environment('local'))
            <link rel="stylesheet" href="{{ mix('css/app.css') }}">
        @else
            <link rel="stylesheet" href="css/app.css">
        @endif

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="scroll-bar font-sans antialiased bg-main">
        @inertia
    </body>
</html>
