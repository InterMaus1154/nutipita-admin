<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Invoices">
            <flux:link href="{{route('invoices.create-manual')}}">
                <flux:icon.plus-circle class="size-8"/>
            </flux:link>
        </x-page-heading>
        <x-success />
        <x-error />
        <div class="grid gap-8">
            <livewire:invoice.invoice-filter />
            <livewire:invoice.invoice-list />
        </div>
    </x-page-section>
</x-flux-layout>
