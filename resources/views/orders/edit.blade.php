<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Edit Order #{{$order->order_id}}"/>
        <div class="flex gap-2">
            <flux:badge>Customer:</flux:badge>
            <flux:link href="{{route('customers.show', ['customer' => $order->customer])}}">{{$order->customer->customer_name}}</flux:link>
        </div>
        <x-error/>
        <x-success />
        <form action="{{route('orders.update', compact('order'))}}" method="POST" class="flex flex-col gap-4">
            @csrf
            @method('PUT')
            <div class="max-w-sm">
                <label for="order_placed_at" class="block text-sm font-medium mb-2 dark:text-white">Order Placed At</label>
                <input type="date" id="order_placed_at" name="order_placed_at"
                       value="{{$order->order_placed_at}}" class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
            </div>
            <div class="max-w-sm">
                <label for="order_due_at" class="block text-sm font-medium mb-2 dark:text-white">Order Due At</label>
                <input type="date" id="order_due_at" name="order_due_at" value="{{$order->order_due_at}}" class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
            </div>
            <div class="max-w-sm">
                <label for="order_status" class="block text-sm font-medium mb-2 dark:text-white">Order Status</label>
                <select name="order_status" id="order_status" class="py-3 px-4 pe-9 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-transparent dark:text-neutral-400 dark:focus:ring-neutral-600">
                    @foreach(\App\Enums\OrderStatus::cases() as $status)
                        <option
                            value="{{$status->name}}"
                            {{$status->name === $order->order_status ? "selected" : ""}}>
                            {{$status->value}}
                        </option>
                    @endforeach
                </select>
            </div>
            <h3>Products</h3>
            @foreach($products as $product)
                @php
                    $orderProduct = $order->products->firstWhere('product_id', $product->product_id);
                @endphp
                <div class="max-w-sm">
                    <label for="products[{{$product->product_id}}]" class="block text-sm font-medium mb-2 dark:text-white">{{$product->product_name}} -
                        £{{$product->price}}</label>
                    <input type="number"
                           id="products[{{$product->product_id}}]"
                           name="products[{{$product->product_id}}]"
                           value="{{$orderProduct ? $orderProduct->pivot->product_qty : 0}}"
                           class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                </div>
            @endforeach
            <flux:button variant="primary" type="submit">Update</flux:button>
        </form>
    </x-page-section>
</x-flux-layout>
