<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Orders">
            <div class="flex gap-4">
                <livewire:order-summary-download />
                <flux:link href="{{route('orders.create')}}" class="cursor-pointer" title="Add new order">
                    <flux:icon.plus-circle class="size-8" />
                </flux:link>
            </div>
        </x-page-heading>
        <x-success/>
        <x-error />
        <div class="flex flex-col gap-8">
            <livewire:order-filter />
            <livewire:order-list :withSummaryPdf="true" :summary-visible-by-default="true"/>
        </div>
    </x-page-section>
</x-flux-layout>
