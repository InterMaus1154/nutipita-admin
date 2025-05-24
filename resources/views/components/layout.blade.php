<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nuti Pita Order Management</title>
{{--    <link rel="stylesheet" href="{{asset('css/reset.css')}}"/>--}}
{{--    <link rel="stylesheet" href="{{asset('css/layout.css')}}"/>--}}
{{--    <link rel="stylesheet" href="{{asset('css/form.css')}}"/>--}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />
    @livewireScripts
    @fluxAppearance
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800">
<x-sidebar />
<main>
{{--    {{$slot}}--}}
</main>
@fluxScripts
</body>
</html>
