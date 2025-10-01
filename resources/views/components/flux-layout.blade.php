@php
    use App\Enums\settings\UserColorMode;
    use App\Enums\settings\UserThemeMode;

    $settings = auth()->user()?->settings;
    $fontSize = $settings?->user_font_size->value ?? 16;
    $colorMode = $settings?->user_color_mode->value ?? 0;
    $themeMode = $settings?->user_theme_mode->value ?? 0;
@endphp

<!doctype html>
<html lang="en"
      data-theme="{{UserColorMode::colorFromValue($colorMode)}}"
      class="{{UserThemeMode::themeFromValue($themeMode)}}"
      data-text="{{$fontSize}}"
>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet"/>
    <link rel="icon" href="{{asset('images/icon_96x96.png?v=3')}}">
    <link rel="apple-touch-icon" href="{{asset('images/icon_96x96.png?v=3')}}" sizes="96x96">
    <meta name="robots" content="noindex">
    @vite(['resources/css/app.css'])
    @livewireStyles
    <title>Nuti Pita: Order Management</title>
</head>
<body class="bg-white dark:bg-zinc-800 min-h-screen dark:text-white">
<x-sidebar />
<flux:main>
    {{$slot}}
</flux:main>
@fluxScripts
@livewireScripts
</body>
</html>

