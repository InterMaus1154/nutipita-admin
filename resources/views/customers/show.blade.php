<x-layout>
    <section class="page-section">
        <h2 class="section-title">Customer Details</h2>
        <x-success/>
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

    </section>
</x-layout>
