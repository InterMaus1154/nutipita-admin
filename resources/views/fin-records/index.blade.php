<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Money Management">
            <div class="flex gap-2 items-center">
                <flux:link href="{{route('financial-categories.index')}}">
                    <flux:icon.layout-list class="size-8" />
                </flux:link>
                <flux:link>
                    <flux:icon.plus-circle class="size-8" />
                </flux:link>
            </div>
        </x-page-heading>
    </x-page-section>
</x-flux-layout>
