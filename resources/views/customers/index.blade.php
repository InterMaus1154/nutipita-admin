<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Customers">
            <div class="flex gap-4">
                <livewire:customer.mobile-customer-sort />
                <flux:link href="{{route('customers.create')}}">
                    <flux:icon.plus-circle class="size-8"/>
                </flux:link>
            </div>
        </x-page-heading>
        <livewire:customer.customer-list />
    </x-page-section>
</x-flux-layout>
