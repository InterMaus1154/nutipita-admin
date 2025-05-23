<x-layout>
    <section class="page-section">
        <h2 class="section-title">Customer Details</h2>
        <x-success/>
        <x-error/>
        <h3>{{$customer->customer_name}}</h3>
        <a href="{{route('customers.edit', compact('customer'))}}" class="action-link">Update customer</a>
        <div class="table-wrapper">
            <table>
                <tbody>
                <tr>
                    <td>Address</td>
                    <td>
                        @if($customer->customer_address)
                            {{$customer->customer_address}}
                        @else
                            <em>No address provided</em>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td>
                        @if($customer->customer_email)
                            <a class="action-link"
                               href="mailto:{{$customer->customer_email}}">{{$customer->customer_email}}</a>
                        @else
                            <em>No email provided</em>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Phone</td>
                    <td>
                        @if($customer->customer_phone)
                            <a class="action-link"
                               href="tel:{{$customer->customer_phone}}">{{$customer->customer_phone}}</a>
                        @else
                            <em>No phone provided</em>
                        @endif
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        {{--custom prices section--}}
        <h2 class="section-title">Custom prices</h2>
        <a href="{{route('customers.create.custom-price', compact('customer'))}}" class="action-link">Add custom
            prices
        </a>
        @livewire('customer-custom-prices', ['customer' => $customer])
        {{--custom orders section--}}
        <h2 class="section-title">Customer Orders</h2>
        <a href="{{route('orders.create', ['customer_id' => $customer->customer_id])}}" class="action-link">Add new
            order</a>
        @if(collect($customer->orders)->isEmpty())
            <em>This customer hasn't placed an order yet!</em>
        @else
            <div class="table-wrapper">
                <table>
                    <thead>
                    <tr>
                        <th>
                            ID
                        </th>
                        <th>
                            Status
                        </th>
                        <th>
                            Placed At
                        </th>
                        <th>
                            Due At
                        </th>
                        <th>
                            Total Pita
                        </th>
                        <th>
                            Total £
                        </th>
                        <th>
                            Edit
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($customer->orders as $order)
                        <tr>
                            <td>
                                <a class="action-link" href="{{route('orders.show', compact('order'))}}">
                                    {{$order->order_id}}
                                </a>
                            </td>
                            <td>
                                {{$order->order_status}}
                            </td>
                            <td>
                                {{$order->order_placed_at}}
                            </td>
                            <td>
                                {{$order->order_due_at ?? "No due date"}}
                            </td>
                            <td>
                                {{$order->total_pita}}
                            </td>
                            <td>
                                £{{$order->total_price}}
                            </td>
                            <td><em>Edit</em></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</x-layout>
