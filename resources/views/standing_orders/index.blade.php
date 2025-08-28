<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Standing Orders">
            <div class="flex gap-4">
                <livewire:standing-order.mobile-standing-order-sort />
                <flux:link href="{{route('standing-orders.create')}}">
                    <flux:icon.plus-circle class="size-8"/>
                </flux:link>
            </div>
        </x-page-heading>
        <livewire:standing-order-list/>
    </x-page-section>
</x-flux-layout>
