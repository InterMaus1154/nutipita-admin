<header>
    <h1>Nuti Pita</h1>
    <nav>
        <ul>
            <li>
                <a href="{{route('admin.view.dashboard')}}">Home</a>
            </li>
            <li>
                <a href="{{route('orders.index')}}">Orders</a>
            </li>
            <li>
                <a href="{{route('standing-orders.index')}}">Standing Orders</a>
            </li>
            <li>
                <a href="{{route('products.index')}}">Products</a>
            </li>
            <li>
                <a href="{{route('customers.index')}}">Customers</a>
            </li>
            <li>
                <form method="POST" action="{{route('auth.logout')}}">
                    @csrf
                    <input title="Logout" aria-label="Logout" type="submit" value="Logout" class="form-submit-button">
                </form>
            </li>
        </ul>
    </nav>
</header>
