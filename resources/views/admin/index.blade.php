@use(Carbon\Carbon)
@use(Carbon\WeekDay)
<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Today Orders">
            <div class="flex gap-4 items-center">
                <livewire:homepage.download-summary />
                <flux:link href="{{route('orders.create')}}" class="cursor-pointer" title="Add new order">
                    <flux:icon.plus-circle class="size-8"/>
                </flux:link>
            </div>
        </x-page-heading>
        <livewire:homepage.homepage-orders/>
    </x-page-section>
</x-flux-layout>
