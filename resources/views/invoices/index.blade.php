<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Invoices">
            <div class="flex gap-4">
                <livewire:invoice.mobile-invoice-sort />
                <flux:link href="{{route('invoices.create')}}">
                    <flux:icon.plus-circle class="size-8"/>
                </flux:link>
            </div>
        </x-page-heading>
        <x-success />
        <x-error />
        <div class="flex flex-col gap-8">
            <livewire:invoice.invoice-filter />
            <livewire:invoice.invoice-list />
        </div>
    </x-page-section>
</x-flux-layout>
