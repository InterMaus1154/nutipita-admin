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
