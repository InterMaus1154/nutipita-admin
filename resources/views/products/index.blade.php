<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Products">
            <div class="flex gap-4">
                <livewire:product.mobile-product-sort />
                <flux:link href="{{route('products.create')}}">
                    <flux:icon.plus-circle class="size-8"/>
                </flux:link>
            </div>
        </x-page-heading>
        <livewire:product.product-list/>
    </x-page-section>
</x-flux-layout>
