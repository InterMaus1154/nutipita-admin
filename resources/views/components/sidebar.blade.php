@use(Illuminate\Support\Facades\Route)
<flux:sidebar sticky stashable
              class="bg-zinc-50 dark:bg-zinc-900 border-r rtl:border-r-0 rtl:border-l border-zinc-200 dark:border-zinc-700">
    <flux:sidebar.toggle id="toggleSidebarButton" icon="x-mark"/>

    <flux:brand href="{{route('admin.view.dashboard')}}" logo="{{asset('images/icon_96x96.png?v=3')}}" name="Nuti Pita"
                class="px-2 dark:hidden"/>
    <flux:brand href="{{route('admin.view.dashboard')}}" logo="{{asset('images/icon_96x96.png?v=3')}}" name="Nuti Pita"
                class="px-2 hidden dark:flex"/>

    <flux:navlist variant="outline">
        <flux:navlist.item icon="home" href="{{route('admin.view.dashboard')}}"
                           :current="Route::is('admin.view.dashboard')">Home
        </flux:navlist.item>
        <flux:navlist.item icon="building-storefront" href="{{route('orders.index')}}" :current="Route::is('orders.*')">
            Orders
        </flux:navlist.item>
        <flux:navlist.item icon="file-text" href="{{route('invoices.index')}}" :current="Route::is('invoices.*')">
            Invoices
        </flux:navlist.item>
        <flux:navlist.item icon="currency-pound" href="{{route('financial-records.index')}}"
                           :current="request()->is('financial-records*')">Money
            Management
        </flux:navlist.item>
        <flux:navlist.item icon="arrow-path-rounded-square" href="{{route('standing-orders.index')}}"
                           :current="Route::is('standing-orders.*')">Standing Orders
        </flux:navlist.item>
        <flux:navlist.item icon="user-circle" href="{{route('customers.index')}}" :current="Route::is('customers.*')">
            Customers
        </flux:navlist.item>
        <flux:navlist.item icon="pita" href="{{route('products.index')}}" :current="Route::is('products.*')">
            Products
        </flux:navlist.item>
        <flux:navlist.item :current="Route::is('settings.*')" icon="cog-6-tooth" x-data="{menuOpen: false}"
                           x-on:click="menuOpen= true" class="relative cursor-pointer">
            Settings
            <flux:icon.chevron-right class="size-5 text-accent transition-transform duration-300"
                                     x-bind:class="{'rotate-90': menuOpen}"
            />
            <div x-show="menuOpen"
                 x-transition
                 x-on:click.outside="menuOpen = false"
                 x-cloak
                 class="absolute z-100 left-[-50%] translate-x-[50%] top-10 border-2 border-neutral-700 rounded-xl bg-zinc-800/60 backdrop-blur-lg p-2 flex flex-col gap-4 w-full action">
                <ul>
                    <li>
                        <x-ui.mobile-card-dropdown-link href="{{route('settings.appearance')}}"
                                                        class="flex! gap-2 py-2">
                            <flux:icon.monitor class="text-black dark:text-white size-4"/>
                            Appearance
                        </x-ui.mobile-card-dropdown-link>
                    </li>
                </ul>
            </div>
        </flux:navlist.item>
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

<flux:header class="grid grid-cols-[1fr_1fr] sm:grid-cols-[1fr_auto_1fr] items-center z-100 bg-zinc-800 shadow-md py-2"
             sticky>
    <flux:sidebar.toggle icon="bars-2" inset="left" class="sm:hidden"/>
    <div class="justify-self-start! max-sm:justify-self-end! flex gap-2 items-center sm:flex-row-reverse">
        <div>
            <span class="font-bold ">
                Week {{getCurrentWeekNumber()}} @if(!app()->isProduction()) <span class="text-red-500">DEBUG</span>

                @endif
            </span>
        </div>
        <flux:dropdown position="top" align="right">
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
    </div>
    <div class="flex gap-4 max-sm:hidden">
        <flux:navlist variant="outline" class="flex-row! gap-4!">
            <flux:navlist.item icon="home" href="{{route('admin.view.dashboard')}}" title="Dashboard"
                               :current="Route::is('admin.view.dashboard')"></flux:navlist.item>
            <flux:navlist.item icon="building-storefront" href="{{route('orders.index')}}" title="Orders"
                               :current="Route::is('orders.*')"></flux:navlist.item>
            <flux:navlist.item icon="file-text" href="{{route('invoices.index')}}" title="Invoices"
                               :current="Route::is('invoices.*')"></flux:navlist.item>
            <flux:navlist.item icon="currency-pound" href="{{route('financial-records.index')}}"
                               title="Money Management"
                               :current="request()->is('financial-records*')"></flux:navlist.item>
            <flux:navlist.item icon="arrow-path-rounded-square" href="{{route('standing-orders.index')}}"
                               title="Standing orders" :current="Route::is('standing-orders.*')"></flux:navlist.item>
            <flux:navlist.item icon="user-circle" href="{{route('customers.index')}}" title="Customers"
                               :current="Route::is('customers.*')"></flux:navlist.item>
            <flux:navlist.item icon="pita" href="{{route('products.index')}}" title="Products"
                               :current="Route::is('products.*')"></flux:navlist.item>
            <flux:navlist.item :current="Route::is('settings.*')" icon="cog-6-tooth" x-data="{menuOpen: false}"
                               x-on:click="menuOpen = true" class="cursor-pointer relative">
                <div x-show="menuOpen"
                     x-transition
                     x-on:click.outside="menuOpen = false"
                     x-cloak
                     class="absolute z-100 left-[-70%] top-8 border-2 border-neutral-700 rounded-xl bg-zinc-800/60 backdrop-blur-lg p-2 flex flex-col gap-4 w-fit action">
                    <ul>
                        <li>
                            <x-ui.mobile-card-dropdown-link href="{{route('settings.appearance')}}" class="flex! gap-2 py-2">
                                <flux:icon.monitor class="text-black dark:text-white size-4"/>
                                Appearance
                            </x-ui.mobile-card-dropdown-link>
                        </li>
                    </ul>
                </div>
            </flux:navlist.item>
        </flux:navlist>
    </div>
    <div class="max-sm:hidden"></div>
</flux:header>
