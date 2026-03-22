@use(Carbon\Carbon)
@use(Carbon\WeekDay)
<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Today Orders">
            <div class="flex gap-4 items-center">
                <livewire:homepage.download-summary/>
                <button class="cursor-pointer" onclick="Livewire.dispatch('modal-open', {
                    component: 'modal.order-create', componentData: {}
                })">
                    <flux:icon.plus-circle class="size-8 text-accent"/>
                </button>
            </div>
        </x-page-heading>
        <livewire:homepage.homepage-orders/>
    </x-page-section>
</x-flux-layout>
