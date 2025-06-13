<x-flux-layout>
    <section class="flex flex-col gap-4">
        <flux:heading size="xl">Orders</flux:heading>
        <flux:separator variant="subtle"/>
        <flux:link href="{{route('orders.create')}}" class="action-link">Add new order</flux:link>
        <x-success/>
        <x-error />
        <livewire:order-filter />
        <livewire:order-list />
    </section>
</x-flux-layout>
