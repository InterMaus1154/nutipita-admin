<x-flux-layout>
    <section class="flex flex-col gap-4">
        <x-page-heading title="Orders"/>
        <flux:link href="{{route('orders.create')}}" class="action-link">Add new order</flux:link>
        <x-success/>
        <x-error />
        <livewire:order-filter />
        <livewire:order-list />
    </section>
</x-flux-layout>
