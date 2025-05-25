<!doctype html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nuti Pita Order Management</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />
    @livewireScripts
    @fluxAppearance
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800">
<x-sidebar />
<x-header />
<flux:main>
    {{$slot}}
</flux:main>
@fluxScripts
</body>
</html>
