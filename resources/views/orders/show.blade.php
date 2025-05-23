<x-layout>
    <section class="page-section">
        <h2 class="section-title">Order #{{$order->order_id}}</h2>
        <div class="table-wrapper">
            <table>
                <tbody>
                <tr>
                    <td>Customer</td>
                    <td>
                        <a href="{{route('customers.show', ['customer' => $order->customer])}}" class="action-link">
                            {{$order->customer->customer_name}}
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>Placed At</td>
                    <td>{{$order->order_placed_at}}</td>
                </tr>
                <tr>
                    <td>Due At</td>
                    <td>{{$order->order_due_at ?? "No specified due date"}}</td>
                </tr>
                <tr>
                    <td>Status</td>
                    <td>{{$order->status}}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </section>
</x-layout>
