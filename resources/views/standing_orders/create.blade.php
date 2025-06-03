<x-layout>
    <div class="form-wrapper standing-order-form">
        <h2 class="form-title">Add new standing order</h2>
        <x-error/>
        <form action="{{route('standing-orders.store')}}" method="POST">
            @csrf
            <div class="input-wrapper">
                <label for="customer_id">Customer</label>
                <select name="customer_id" id="customer_id">
                    <option value="">---Select customer---</option>
                    @foreach($customers as $customer)
                        <option value="{{$customer->customer_id}}">{{$customer->customer_name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="input-wrapper">
                <label for="start_from">Start From</label>
                <input type="date" id="start_from" name="start_from"
                       value="{{old('start_from', now()->toDateString())}}">
            </div>
            <div class="days-wrapper">
                {{--count the days from 0 to 7--}}
                @for($i = 0; $i<7;$i++)
                    <div class="day-wrapper">
                        <h4>{{\Illuminate\Support\Carbon::create()->startOfWeek()->addDays($i)->dayName}}</h4>
                        @foreach($products as $product)
                            <div class="input-wrapper">
                                <label
                                    for="products[{{$i}}][{{$product->product_id}}]">{{$product->product_name}}</label>
                                <input
                                    type="number"
                                    value="0"
                                    id="products[{{$i}}][{{$product->product_id}}]"
                                    name="products[{{$i}}][{{$product->product_id}}]"
                                >
                            </div>
                        @endforeach
                    </div>
                @endfor
            </div>
            <input type="submit" class="form-submit-button" value="Add">
        </form>
    </div>
</x-layout>
