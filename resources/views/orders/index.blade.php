<x-layout>
    <section class="page-section">
        <h2 class="section-title">Orders</h2>
        <x-success/>
        <div class="order-filter">
            <h3>Filter & Sort</h3>
        </div>
        <a href="{{route('orders.create')}}" class="action-link">Add new order</a>
        <div class="table-wrapper">
            <table>
                <thead>
                <tr>
                    <th>
                        ID
                    </th>
                    <th>
                        Customer Name
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
                </tr>
                </thead>
                <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>
                            <a class="action-link" href="">
                                {{$order->order_id}}
                            </a>
                        </td>
                        <td>
                            <a href="{{route('customers.show', ['customer' => $order->customer])}}" class="action-link">
                                {{$order->customer->customer_name}}
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
                    </tr>
                @empty
                    <tr style="text-align: center">
                        <td>No orders found!</td>
                    </tr>
                @endforelse

                </tbody>
            </table>
        </div>
    </section>
</x-layout>
