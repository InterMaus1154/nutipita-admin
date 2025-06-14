<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Products"/>
        <x-success/>
        <x-error/>
        <flux:link href="{{route('products.create')}}">Add new product</flux:link>
        <div class="table-wrapper">
            <table>
                <thead>
                <tr>
                    <th>
                        ID
                    </th>
                    <th>
                        Name
                    </th>
                    <th>
                        Unit Weight
                    </th>
                    <th>
                        Qty / pack
                    </th>
                </tr>
                </thead>
                <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>
                            <a class="action-link" href="{{route('products.show', compact('product'))}}">
                                {{$product->product_id}}
                            </a>
                        </td>
                        <td>{{$product->product_name}}</td>
                        <td>
                            @if($product->product_weight_g)
                                {{$product->product_weight_g}}g
                            @else
                                <em>No specified weight</em>
                            @endif
                        </td>
                        <td>
                            @if($product->product_qty_per_pack)
                                {{$product->product_qty_per_pack}}pcs
                            @else
                                <em>No specified pack quantity</em>
                            @endif
                        </td>
                        <td>
                            <a class="action-link" href="{{route('products.edit', compact('product'))}}">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr style="text-align: center">
                        <td>No products found!</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="flex flex-col">
            <div class="-m-1.5 overflow-x-auto">
                <div class="p-1.5 min-w-full inline-block align-middle">
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                            <thead>
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">
                                    ID
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">
                                    Name
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">
                                    Unit Weight
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">
                                    Qty / Pack
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">
                                    Actions
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($products as $product)
                                <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-100 dark:odd:bg-neutral-800 dark:even:bg-neutral-700 dark:hover:bg-neutral-700 text-center">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                        <flux:link href="{{route('products.show', compact('product'))}}">
                                            #{{$product->product_id}}</flux:link>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                        <flux:link
                                            href="{{route('products.show', compact('product'))}}">{{$product->product_name}}</flux:link>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                        @if($product->product_weight_g)
                                            {{$product->product_weight_g}}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                        @if($product->product_qty_per_pack)
                                            {{$product->product_qty_per_pack}}pcs
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200 space-x-1.5">
                                        <flux:link
                                            href="{{route('products.show', compact('product'))}}">View
                                        </flux:link>
                                        <flux:link
                                            href="{{route('products.edit', compact('product'))}}">Edit
                                        </flux:link>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </x-page-section>
</x-flux-layout>
