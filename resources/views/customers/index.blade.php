<x-layout>
    <section class="page-section">
        <h2 class="section-title">Customers</h2>
        <a href="{{route('customers.create')}}" class="action-link">Add new customer</a>
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
                        Address
                    </th>
                    <th>
                        Email
                    </th>
                    <th>
                        Phone
                    </th>
                    <th>
                        Orders
                    </th>
                </tr>
                </thead>
                <tbody>
                @forelse($customers as $customer)
                    <tr>
                        <td>
                            <a href="{{route('customers.show', compact('customer'))}}">
                                {{$customer->customer_id}}
                            </a>
                        </td>
                        <td>{{$customer->customer_name}}</td>
                        <td>
                            @if($customer->customer_address)
                                {{$customer->customer_address}}
                            @else
                                <em>No address provided</em>
                            @endif
                        </td>
                        <td>
                            @if($customer->customer_email)
                                <a href="mailto:{{$customer->customer_email}}">{{$customer->customer_email}}</a>
                            @else
                                <em>No email provided</em>
                            @endif
                        </td>
                        <td>
                            @if($customer->customer_phone)
                                <a href="tel:{{$customer->customer_phone}}">{{$customer->customer_phone}}</a>
                            @else
                                <em>No phone provided</em>
                            @endif
                        </td>
                        <td>{{$customer->orders_count}}</td>
                    </tr>
                @empty
                    <tr style="text-align: center">
                        <td>No customers found!</td>
                    </tr>
                @endforelse

                </tbody>
            </table>
        </div>
    </section>
</x-layout>
