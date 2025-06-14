<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Invoices"/>
        <x-success />
        <x-error />
        <flux:link href="{{route('invoices.create')}}">Create New Invoice From Order</flux:link>
        <flux:link href="{{route('invoices.create-manual')}}">Create Manual Invoice</flux:link>
        @livewire('invoice-list')
    </x-page-section>
</x-flux-layout>
