<x-flux-layout>
    <x-page-section class="flex flex-col gap-8">
        <x-page-heading title="Orders">
            <flux:link href="{{route('orders.create')}}" class="action-link" title="Add new order">
                <flux:icon.plus-circle class="size-8" />
            </flux:link>
        </x-page-heading>
        <x-success/>
        <x-error />
        <livewire:order-filter />
        <livewire:order-list :withSummaryPdf="true"/>
    </x-page-section>
</x-flux-layout>
