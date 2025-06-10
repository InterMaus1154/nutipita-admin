<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{asset('css/reset.css')}}">
    <link rel="stylesheet" href="{{asset('css/login.css')}}">
    <link rel="stylesheet" href="{{asset('css/form.css')}}">
    <link rel="icon" href="{{asset('images/icon_96x96.png?v=3')}}">
    <meta name="robots" content="noindex" >
    @vite(['resources/css/app.css'])
    @fluxAppearance
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
</body>
</html>
