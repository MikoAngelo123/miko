<ul class="nav flex-column">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}" href="{{ route('customer.dashboard') }}">
            <span data-feather="home"></span>
            Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('customer.products') ? 'active' : '' }}" href="{{ route('customer.products') }}">
            <span data-feather="box"></span>
            Browse Products
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('customer.cart') ? 'active' : '' }}" href="{{ route('customer.cart') }}">
            <span data-feather="shopping-cart"></span>
            View Cart
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('customer.orders') ? 'active' : '' }}" href="{{ route('customer.orders') }}">
            <span data-feather="file-text"></span>
            View Orders
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('customer.settings') ? 'active' : '' }}" href="{{ route('customer.settings') }}">
            <span data-feather="settings"></span>
            Account Settings
        </a>
    </li>
</ul>
