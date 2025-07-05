<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Standing Orders"/>
        <flux:link href="{{route('standing-orders.create')}}">Add new standing order</flux:link>
        <livewire:standing-order-list/>
    </x-page-section>
</x-flux-layout>
