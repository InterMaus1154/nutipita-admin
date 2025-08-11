<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Add new standing order"/>
        <x-success/>
        <x-error/>
        <form action="{{route('standing-orders.store')}}" method="POST" class="flex flex-col gap-4">
            @csrf
            <div class="flex gap-4 flex-wrap">
                <x-form.form-wrapper center="true">
                    <label for="customer_id" class="block text-sm font-medium mb-2 dark:text-white">Customer</label>
                    <select name="customer_id" id="customer_id"
                            class="py-3 px-4 pe-9 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-transparent dark:text-neutral-400 dark:focus:ring-neutral-600">
                        <option value=""></option>
                        @foreach($customers as $customer)
                            <option value="{{$customer->customer_id}}">{{$customer->customer_name}}</option>
                        @endforeach
                    </select>
                </x-form.form-wrapper>
                <x-form.form-wrapper center="true">
                    <label for="start_from" class="block text-sm font-medium mb-2 dark:text-white">Start From</label>
                    <input type="date" id="start_from" name="start_from"
                           value="{{old('start_from', now()->toDateString())}}"
                           class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                </x-form.form-wrapper>
            </div>
            <div class="flex gap-8 flex-wrap my-2">
                {{--count the days from 0 to 7--}}
                @for($i = 0; $i<7;$i++)
                    <div
                        class="flex flex-col gap-4 bg-white border border-gray-200 shadow-2xs rounded-xl p-4 md:p-5 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400">
                        <h4 class="font-bold">{{\Illuminate\Support\Carbon::create()->startOfWeek()->addDays($i)->dayName}}</h4>
                        @foreach($products as $product)
                            <div class="max-w-sm">
                                <label
                                    for="products[{{$i}}][{{$product->product_id}}]"
                                    class="block text-sm font-medium mb-2 dark:text-white">{{$product->product_name}}</label>
                                <input
                                    type="number"
                                    value="0"
                                    id="products[{{$i}}][{{$product->product_id}}]"
                                    name="products[{{$i}}][{{$product->product_id}}]"
                                    class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                >
                            </div>
                        @endforeach
                    </div>
                @endfor
            </div>
            <flux:button variant="primary" type="submit">Add</flux:button>
        </form>
    </x-page-section>
</x-flux-layout>
