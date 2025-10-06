<!-- Sidebar -->
<aside id="sidebar" class="bg-white border-end p-3 shadow-sm">
  <!-- Profile Section -->
  <div class="profile-section text-center">
    <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop&crop=face"
         alt="Vendor Profile" class="profile-img mb-2">
    @php $vendor = Auth::guard('vendor')->user(); @endphp
    <h6 class="fw-semibold mb-1">{{ $vendor->name ?? 'Vendor' }}</h6>
    <small class="text-muted">{{ $vendor->company_name ?? 'Company' }}</small>
  </div>

  <!-- Navigation Menu -->
  <ul class="nav flex-column">
    <li class="nav-item">
      <a href="{{ route('vendor.dashboard') }}" class="nav-link text-dark {{ request()->routeIs('vendor.dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2 me-2"></i> Dashboard
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ route('vendor.bidding.landing') }}" class="nav-link text-dark {{ request()->routeIs('vendor.bidding.*') ? 'active' : '' }}">
        <i class="bi bi-gavel me-2"></i> Browse Opportunities
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ route('vendor.bids') }}" class="nav-link text-dark {{ request()->routeIs('vendor.bids') ? 'active' : '' }}">
        <i class="bi bi-file-earmark-text me-2"></i> My Bids
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ route('vendor.contracts') }}" class="nav-link text-dark {{ request()->routeIs('vendor.contracts') ? 'active' : '' }}">
        <i class="bi bi-file-earmark-check me-2"></i> My Contracts
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ route('vendor.orders') }}" class="nav-link text-dark {{ request()->routeIs('vendor.orders') ? 'active' : '' }}">
        <i class="bi bi-cart-check me-2"></i> My Orders
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ route('vendor.invoices') }}" class="nav-link text-dark {{ request()->routeIs('vendor.invoices') ? 'active' : '' }}">
        <i class="bi bi-receipt me-2"></i> My Invoices
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ route('vendor.damage.reports') }}" class="nav-link text-dark {{ request()->routeIs('vendor.damage.*') ? 'active' : '' }}">
        <i class="bi bi-exclamation-triangle me-2"></i> Damage Reports
        @php
          $damageCount = 0;
          if ($vendor) {
            $damageCount = App\Models\InventoryReceiptItem::whereHas('receipt', function($q) use ($vendor) {
              $q->where('supplier_name', $vendor->company_name);
            })
            ->where('damaged_quantity', '>', 0)
            ->where('return_to_vendor', true)
            ->whereNull('acknowledged_at')
            ->count();
          }
        @endphp
        @if($damageCount > 0)
        <span class="badge bg-danger rounded-pill ms-1">{{ $damageCount }}</span>
        @endif
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ route('vendor.profile') }}" class="nav-link text-dark {{ request()->routeIs('vendor.profile') ? 'active' : '' }}">
        <i class="bi bi-person me-2"></i> My Profile
      </a>
    </li>
    <li class="nav-item mt-3">
      <a href="{{ route('vendor.logout') }}" class="nav-link text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="bi bi-box-arrow-right me-2"></i> Logout
      </a>
    </li>
  </ul>
</aside>

<!-- Logout Form -->
<form id="logout-form" action="{{ route('vendor.logout') }}" method="POST" style="display: none;">
  @csrf
</form>

<!-- Overlay for mobile -->
<div id="overlay" class="position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50" style="z-index:1040; display: none;"></div>
