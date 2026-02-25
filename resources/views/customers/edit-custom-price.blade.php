<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Prices for {{$customer->customer_name}}"/>
        <div class="flex items-start">
            <flux:badge icon="exclamation-circle">0 or empty will be deleted</flux:badge>
        </div>
        <x-error/>
        <x-success/>
        <form action="{{route('customers.update.custom-price', compact('customer'))}}" method="POST"
              class="flex flex-col gap-4" autocomplete="off">
            @csrf
            @method('PUT')
            @foreach($products as $product)
                <div class="max-w-sm">
                    <label for="products[{{$product->product_id}}]"
                           class="block text-sm font-medium mb-2 dark:text-white">{{$product->product_name}}
                        <flux:badge>
                            {{$product->product_weight_g}}g
                        </flux:badge>
                    </label>
                    <input type="number"
                           id="products[{{$product->product_id}}]"
                           name="products[{{$product->product_id}}]"
                           placeholder="Unit price"
                           value="{{old('products.'.$product->product_id, '')}}"
                           step="0.0001"
                           class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                    >
                </div>
            @endforeach
            <flux:button variant="primary" type="submit">Submit</flux:button>
        </form>
    </x-page-section>
</x-flux-layout>

