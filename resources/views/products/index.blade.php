<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Products">
            <flux:link href="{{route('products.create')}}">
                <flux:icon.plus-circle class="size-8"/>
            </flux:link>
        </x-page-heading>
        <livewire:product.product-list/>
    </x-page-section>
</x-flux-layout>
