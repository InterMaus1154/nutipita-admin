<x-layout>
    <section class="page-section">
        <h2 class="section-title">Orders</h2>
        <x-success/>
        <livewire:order-filter />
        <a href="{{route('orders.create')}}" class="action-link">Add new order</a>
        <livewire:order-list />
    </section>
</x-layout>
