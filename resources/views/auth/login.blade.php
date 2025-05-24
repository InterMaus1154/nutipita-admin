<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet"/>
    @livewireScripts
    @fluxAppearance
    @vite('resources/css/app.css')
    <title>Login</title>
</head>
<body>
<div class="min-h-screen flex justify-center items-center">
    <div class="flex gap-4 flex-col border border-gray-300 rounded-lg p-10">
        <flux:heading size="xl" class="text-center">Login</flux:heading>
        <flux:error></flux:error>
        <div class="space-y-6">
            <form method="POST" action="{{route('auth.login')}}" class="space-y-6">
                @csrf
                <flux:input name="username" label="Username" type="text" placeholder="Your username" value="{{old('username', '')}}"/>
                <flux:field>
                    <div class="mb-3 flex justify-between">
                        <flux:label>Password</flux:label>
                    </div>
                    <flux:input name="password" type="password" placeholder="Your password"/>
                </flux:field>
                <div class="space-y-2">
                    <flux:button type="submit">Login</flux:button>
                </div>
            </form>

        </div>
    </div>
</div>
@fluxScripts
</body>
</html>
