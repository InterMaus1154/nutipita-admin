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
    @vite('resources/css/app.css')
    @livewireScripts
    <title>Login</title>
</head>
<body class="bg-white dark:bg-zinc-800">
<div class="min-h-screen flex justify-center items-center">
    <div class="mt-7 bg-white border border-gray-200 rounded-xl shadow-2xs dark:bg-neutral-900 dark:border-neutral-700">
        <div class="p-4 sm:p-7">
            <div class="text-center">
                <h1 class="block text-2xl font-bold text-gray-800 dark:text-white ">Sign in</h1>
            </div>
            <div class="space-y-4">
                <x-error/>
            </div>
            <div class="mt-5">
                <!-- Form -->
                <form method="POST" action="{{route('auth.login')}}">
                    @csrf
                    <div class="grid gap-y-4">
                        <!-- Form Group -->
                        <div>
                            <label for="username" class="block text-sm mb-2 dark:text-white">Username</label>
                            <div class="relative">
                                <input type="text" id="username" name="username"
                                       class="py-2.5 sm:py-3 px-4 block w-full border border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                       required aria-describedby="email-error" placeholder="Username">
                            </div>
                        </div>
                        <!-- End Form Group -->
                        <!-- Form Group -->
                        <div>
                            <div class="flex flex-wrap justify-between items-center gap-2">
                                <label for="password" class="block text-sm mb-2 dark:text-white">Password</label>
                            </div>
                            <div class="relative">
                                <input type="password" id="password" name="password"
                                       class="border py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                       required aria-describedby="password-error" placeholder="Password">
                            </div>
                        </div>
                        <!-- End Form Group -->
                        <flux:button variant="primary" type="submit">Login</flux:button>
                    </div>
                </form>
                <!-- End Form -->
            </div>
        </div>
    </div>
    <flux:button class="!fixed bottom-0 right-0" id="infoBtn">
        <flux:icon.information-circle />
    </flux:button>
</div>
<script>
    const infoBtn = document.querySelector("#infoBtn");
    infoBtn.addEventListener("click", ()=>{
       alert("Szerbusz!");
    });
</script>
@fluxScripts
</body>
</html>
