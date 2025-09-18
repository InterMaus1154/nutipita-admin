<!doctype html>
<html lang="en" class="dark" data-theme="orange">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet"/>
    <link rel="icon" href="{{asset('images/icon_96x96.png?v=3')}}">
    <link rel="apple-touch-icon" href="{{asset('images/icon_96x96.png?v=3')}}" sizes="96x96">
    <meta name="robots" content="noindex">
    @vite('resources/css/app.css')
    @livewireScripts
    <title>Login</title>
</head>
<body class="bg-white dark:bg-zinc-800">
<div class="min-h-screen flex justify-center items-center">
    <div class="bg-white border border-gray-200 rounded-xl dark:bg-neutral-900 dark:border-neutral-700 shadow-xl">
        <div class="p-4 sm:p-7 flex flex-col gap-2">
            <div class="text-center">
                <h1 class="block text-2xl font-bold text-gray-800 dark:text-white ">Login</h1>
            </div>
            <div class="space-y-4">
                <x-error/>
            </div>
            <div>
                <form method="POST" action="{{route('auth.login')}}">
                    @csrf
                    <div class="grid gap-6">
                        <x-form.form-wrapper>
                            <x-form.form-label id="username" text="Username"/>
                            <x-form.form-input id="username" type="text" name="username" placeholder="Username"/>
                        </x-form.form-wrapper>
                        <x-form.form-wrapper>
                            <x-form.form-label id="password" text="Password"/>
                            <x-form.form-input id="password" type="password" name="password" placeholder="Password"/>
                        </x-form.form-wrapper>
                        <flux:button variant="primary" type="submit">Login</flux:button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <flux:button class="!fixed bottom-0 right-0" id="infoBtn">
        <flux:icon.information-circle/>
    </flux:button>
</div>
<script>
    const infoBtn = document.querySelector("#infoBtn");
    infoBtn.addEventListener("click", () => {
        alert("Szerbusz!");
    });
</script>
@fluxScripts
</body>
</html>
