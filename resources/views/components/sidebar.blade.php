@use(Illuminate\Support\Facades\Route)
<flux:sidebar sticky stashable
              class="bg-zinc-50 dark:bg-zinc-900 border-r rtl:border-r-0 rtl:border-l border-zinc-200 dark:border-zinc-700">
    <flux:sidebar.toggle class="lg:hidden" id="toggleSidebarButton" icon="x-mark"/>

    <flux:brand href="{{route('admin.view.dashboard')}}" logo="{{asset('images/icon_96x96.png?v=3')}}" name="Nuti Pita" class="px-2 dark:hidden"/>
    <flux:brand href="{{route('admin.view.dashboard')}}" logo="{{asset('images/icon_96x96.png?v=3')}}" name="Nuti Pita"
                class="px-2 hidden dark:flex"/>

    <flux:navlist variant="outline">
        <flux:navlist.item icon="home" href="{{route('admin.view.dashboard')}}" :current="Route::is('admin.view.dashboard')">Home</flux:navlist.item>
        <flux:navlist.item icon="building-storefront" href="{{route('orders.index')}}" :current="Route::is('orders.*')">Orders</flux:navlist.item>
        <flux:navlist.item icon="banknotes" href="{{route('invoices.index')}}" :current="Route::is('invoices.*')">Invoices</flux:navlist.item>
        <flux:navlist.item icon="currency-pound" href="{{route('money.index')}}" :current="Route::is('money.*')">Income</flux:navlist.item>
        <flux:navlist.item icon="arrow-path-rounded-square" href="{{route('standing-orders.index')}}" :current="Route::is('standing-orders.*')">Standing Orders</flux:navlist.item>
        <flux:navlist.item icon="user-circle" href="{{route('customers.index')}}" :current="Route::is('customers.*')">Customers</flux:navlist.item>
        <flux:navlist.item icon="cake" href="{{route('products.index')}}" :current="Route::is('products.*')">Products</flux:navlist.item>
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
    <flux:dropdown position="top" alignt="start" class="lg:hidden">
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
