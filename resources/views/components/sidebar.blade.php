<flux:sidebar sticky stashable
              class="min-h-screen bg-zinc-50 dark:bg-zinc-900 border-r rtl:border-r-0 rtl:border-l border-zinc-200 dark:border-zinc-700">
    <flux:sidebar.toggle class="lg:hidden" icon="x-mark"/>
    <flux:brand href="#" logo="{{asset('images/logo.png')}}" name="Nuti Pita" class="px-2 dark:hidden"/>
    <flux:brand href="#" logo="{{asset('images/logo.png')}}" name="Nuti Pita"
                class="px-2 hidden dark:flex"/>
    <flux:navlist variant="outline">
        <flux:navlist.item icon="home" href="#" current>Home</flux:navlist.item>
        <flux:navlist.item icon="building-storefront" href="#">Orders</flux:navlist.item>
        <flux:navlist.item icon="beaker" href="#">Products</flux:navlist.item>
        <flux:navlist.item icon="user-group" href="#">Customers</flux:navlist.item>
    </flux:navlist>
    <flux:spacer/>
    <flux:dropdown position="top" align="start" class="max-lg:hidden">
        <flux:profile avatar="{{asset('images/logo.png')}}" name="{{auth()->user()->username}}"/>
        <flux:menu>
            <flux:menu.item icon="arrow-right-start-on-rectangle">
                <form action="{{route('auth.logout')}}" method="POST">
                    @csrf
                    <input type="submit" value="Logout">
                </form>
                </flux:menu.item>
        </flux:menu>
    </flux:dropdown>
</flux:sidebar>
