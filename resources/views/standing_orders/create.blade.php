<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Add new standing order"/>
        <x-success/>
        <x-error/>
        <form action="{{route('standing-orders.store')}}" method="POST" class="flex flex-col gap-4">
            @csrf
            <div class="flex gap-4 flex-wrap">
                {{--customer dropdown--}}
                <x-form.customer-select />
                {{--standing order activation/start date--}}
                <x-form.form-wrapper>
                    <x-form.form-label id="start_from" text="Start From"/>
                    <x-form.form-input type="date" id="start_from" name="start_from" value="{{old('start_from', now()->toDateString())}}" />
                </x-form.form-wrapper>
            </div>
            <div class="flex gap-8 flex-wrap my-2">
                {{--count the days from 0 to 7--}}
                @for($i = 0; $i<7;$i++)
                    <div
                        class="flex flex-col gap-4 bg-white border border-gray-200 shadow-2xs rounded-xl p-4 md:p-5 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400">
                        <h4 class="font-bold">{{\Illuminate\Support\Carbon::create()->startOfWeek()->addDays($i)->dayName}}</h4>
                        @foreach($products as $product)
                            <x-form.form-wrapper>
                                <x-form.form-label id="products[{{$i}}][{{$product->product_id}}]" text="{{$product->product_name}} {{$product->product_weight_g}}g"/>
                            </x-form.form-wrapper>
                            <x-form.form-input type="number" id="products[{{$i}}][{{$product->product_id}}]" name="products[{{$i}}][{{$product->product_id}}]"/>
                        @endforeach
                    </div>
                @endfor
            </div>
            <flux:button variant="primary" type="submit">Add</flux:button>
        </form>
    </x-page-section>
</x-flux-layout>
