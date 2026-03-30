<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Orders">
            <div class="flex gap-4">
                <livewire:order-summary-download/>
                <livewire:order.mobile-order-sort/>
                <button class="cursor-pointer" x-data @click="$dispatch('modal-open', {component: 'modal.order-create'})">
                    <flux:icon.plus-circle class="size-8 text-accent"/>
                </button>
            </div>
        </x-page-heading>
        <div class="flex flex-col gap-8">
            <livewire:order.order-filter/>
            @php
                // set week as default
                    $filters = [
                        'due_from' => now()->startOfWeek(\Carbon\WeekDay::Sunday)->format('Y-m-d'),
                        'due_to' => now()->endOfWeek(\Carbon\WeekDay::Saturday)->format('Y-m-d')
                        ];
            @endphp
            <livewire:order-list :withSummaryPdf="true" :summary-visible-by-default="true" :prop-filters="$filters"/>
        </div>
    </x-page-section>
</x-flux-layout>
