<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Financial Categories">
            <flux:link href="{{route('financial-categories.create')}}">
                <flux:icon.plus-circle class="size-8"/>
            </flux:link>
        </x-page-heading>
        <livewire:fin-categories.financial-category-list />
    </x-page-section>
</x-flux-layout>
