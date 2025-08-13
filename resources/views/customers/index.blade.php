<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Customers">
            <flux:link href="{{route('customers.create')}}">
                <flux:icon.plus-circle class="size-8"/>
            </flux:link>
        </x-page-heading>
        <x-error/>
        <x-success/>
        <livewire:customer.customer-list />
    </x-page-section>
</x-flux-layout>
