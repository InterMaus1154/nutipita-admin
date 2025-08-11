<div class="flex flex-col gap-4">
    <div class="max-w-sm">
        <label for="customer_id" class="block text-sm font-medium mb-2 dark:text-white">Customer</label>
        <select id="customer_id" name="customer_id" wire:model.live="customer_id"
                class="py-3 px-4 pe-9 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-transparent dark:text-neutral-400 dark:focus:ring-neutral-600">
            <option value="">---Select a customer---</option>
            @foreach($customers as $customer)
                <option
                    value="{{$customer->customer_id}}">
                    {{$customer->customer_name}}
                </option>
            @endforeach
        </select>
    </div>
    <div class="flex flex-col gap-4 items-start">
        @forelse($products as $product)
            @if($product->price > 0)
                <div class="input-wrapper">
                    <label for="products-{{$product->product_id}}"
                           class="block text-sm font-medium mb-2 dark:text-white">
                        {{$product->product_name}} {{$product->product_weight_g}}g -
                        <flux:badge>@unitPriceFormat($product->price)</flux:badge>
                    </label>
                    <input type="number"
                           id="products-{{$product->product_id}}"
                           name="products[{{$product->product_id}}]"
                           value="0"
                           placeholder="Quantity"
                           class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                </div>
            @endif
        @empty
            <em>---Please select a customer first!---</em>
        @endforelse
    </div>
</div>
