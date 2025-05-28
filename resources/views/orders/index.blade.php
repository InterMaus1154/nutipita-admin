<x-layout>
    <section class="page-section">
        <h2 class="section-title">Orders</h2>
        <a href="{{route('orders.create')}}" class="action-link">Add new order</a>
        <x-success/>
        <livewire:order-filter />
        <livewire:order-list />
    </section>
</x-layout>
