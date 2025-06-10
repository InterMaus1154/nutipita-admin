<!doctype html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet"/>
    <link rel="icon" href="{{asset('images/icon_96x96.png?v=3')}}">
    <link rel="apple-touch-icon" href="{{asset('images/icon_96x96.png?v=3')}}" sizes="96x96">
    <meta name="robots" content="noindex">
    @fluxAppearance
    @vite(['resources/css/app.css'])
    <title>Login</title>
</head>
<body>
<div class="login-page">
    <div class="form-wrapper">
        <h2 class="form-title">Login</h2>
        <x-error/>
        <form action="{{route('auth.login')}}" method="POST">
            @csrf
            {{--input for username--}}
            <div class="input-wrapper">
                <label for="username">Username</label>
                <input type="text" value="{{old('username', '')}}" name="username" id="username"
                       placeholder="Username">
            </div>
            {{--input for password--}}
            <div class="input-wrapper">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Password">
            </div>
            <input title="Login" type="submit" value="Login" class="form-submit-button">
        </form>
    </div>
</div>
@fluxScripts
@livewireScripts
</body>
</html>
