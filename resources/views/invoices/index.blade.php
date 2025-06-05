<x-layout>
    <section class="page-section">
        <h2 class="section-title">Invoices</h2>
        <a href="{{route('invoices.create')}}" class="action-link">Create New Invoice From Order</a>
        @livewire('invoice-list')
    </section>
</x-layout>
