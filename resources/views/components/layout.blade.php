<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nuti Pita Order Management</title>
    <link rel="stylesheet" href="{{asset('css/reset.css?v=1')}}"/>
    <link rel="stylesheet" href="{{asset('css/layout.css?v=2')}}"/>
    <link rel="stylesheet" href="{{asset('css/form.css?v=1')}}"/>
    <link rel="icon" href="{{asset('images/icon_96x96.png')}}">
    <script src="{{asset('js/nav.js')}}" defer></script>
    @livewireScripts
</head>
<body>
<x-header/>
<main>
    {{$slot}}
</main>
</body>
</html>
