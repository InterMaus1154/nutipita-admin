<flux:sidebar sticky stashable
              class="bg-zinc-50 dark:bg-zinc-900 border-r rtl:border-r-0 rtl:border-l border-zinc-200 dark:border-zinc-700">
    <flux:sidebar.toggle class="lg:hidden" icon="x-mark"/>

    <flux:brand href="#" logo="https://fluxui.dev/img/demo/logo.png" name="Acme Inc." class="px-2 dark:hidden"/>
    <flux:brand href="#" logo="https://fluxui.dev/img/demo/dark-mode-logo.png" name="Acme Inc."
                class="px-2 hidden dark:flex"/>

    <flux:input as="button" variant="filled" placeholder="Search..." icon="magnifying-glass"/>

    <flux:navlist variant="outline">
        <flux:navlist.item icon="home" href="#" current>Home</flux:navlist.item>
        <flux:navlist.item icon="inbox" badge="12" href="#">Inbox</flux:navlist.item>
        <flux:navlist.item icon="document-text" href="#">Documents</flux:navlist.item>
        <flux:navlist.item icon="calendar" href="#">Calendar</flux:navlist.item>

        <flux:navlist.group expandable heading="Favorites" class="hidden lg:grid">
            <flux:navlist.item href="#">Marketing site</flux:navlist.item>
            <flux:navlist.item href="#">Android app</flux:navlist.item>
            <flux:navlist.item href="#">Brand guidelines</flux:navlist.item>
        </flux:navlist.group>
    </flux:navlist>

    <flux:spacer/>

    <flux:dropdown position="top" align="start" class="max-lg:hidden">
        <flux:profile avatar="{{asset('images/icon_96x96.png?v=3')}}" name="{{auth()->user()->username}}"/>
        <flux:menu>
            <flux:menu.item icon="arrow-right-start-on-rectangle" class="w-full">
                <form method="POST" action="{{route('auth.logout')}}" class="w-full">
                    @csrf
                    <input type="submit" value="Logout" class="w-full text-left cursor-pointer"/>
                </form>
            </flux:menu.item>
        </flux:menu>
    </flux:dropdown>
</flux:sidebar>

<flux:header class="lg:hidden">
    <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left"/>
    <flux:spacer/>
    <flux:dropdown position="top" alignt="start">
        <flux:profile avatar="{{asset('images/icon_96x96.png?v=3')}}"/>
        <flux:menu>
            <flux:menu.item icon="arrow-right-start-on-rectangle" class="w-full">
                <form method="POST" action="{{route('auth.logout')}}" class="w-full">
                    @csrf
                    <input type="submit" value="Logout" class="w-full text-left cursor-pointer"/>
                </form>
            </flux:menu.item>
        </flux:menu>
    </flux:dropdown>
</flux:header>
