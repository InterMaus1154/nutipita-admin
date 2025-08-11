<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Standing Orders">
            <flux:link href="{{route('standing-orders.create')}}">
                <flux:icon.plus-circle class="size-8"/>
            </flux:link>
        </x-page-heading>
        <livewire:standing-order-list/>
    </x-page-section>
</x-flux-layout>
