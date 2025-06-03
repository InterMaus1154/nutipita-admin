<x-layout>
    <section class="page-section">
        <h2 class="section-title">Standing Orders</h2>
        <x-success/>
        <a class="action-link" href="{{route('standing-orders.create')}}">Add new standing order</a>
        <div class="table-wrapper">
            <table>
                <thead>
                <tr>
                    <th>
                        ID #
                    </th>
                    <th>
                        Customer
                    </th>
                    <th>
                        Start From
                    </th>
                    <th>
                        Status
                    </th>
                </tr>
                </thead>
                <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>
                            <a class="action-link" href="{{route('standing-orders.show', compact('order'))}}">
                                #{{$order->standing_order_id}}
                            </a>
                        </td>
                        <td>
                            <a href="{{route('customers.show', ['customer' => $order->customer])}}" class="action-link">
                                {{$order->customer->customer_name}}
                            </a>
                        </td>
                        <td>
                            {{$order->start_from}}
                        </td>
                        <td>
                            {{$order->is_active ? "Active" : "Inactive"}}
                        </td>
                        <td>
                            <a href="{{route('standing-orders.edit', compact('order'))}}" class="action-link">Edit</a>
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
