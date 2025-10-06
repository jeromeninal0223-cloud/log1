<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background-color: var(--jetlouge-primary);">
  <div class="container-fluid">
    <button class="sidebar-toggle desktop-toggle me-3" id="desktop-toggle" title="Toggle Sidebar">
      <i class="bi bi-list fs-5"></i>
    </button>
    <a class="navbar-brand fw-bold" href="#">
      <i class="bi bi-building me-2"></i>JetLouge Travels
    </a>
    <div class="d-flex align-items-center">
      <!-- Enhanced Notification Icon -->
      <div class="dropdown me-3">
        <button class="btn btn-link text-white position-relative notification-btn" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="bi bi-bell fs-5 notification-icon"></i>
          @php
            $unreadCount = isset($notifications) ? collect($notifications)->where('read', false)->count() : 0;
          @endphp
          @if($unreadCount > 0)
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge pulse">
            {{ $unreadCount }}
            <span class="visually-hidden">unread notifications</span>
          </span>
          @endif
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow-lg notification-dropdown" aria-labelledby="notificationDropdown">
          <li class="notification-header">
            <div class="d-flex justify-content-between align-items-center px-3 py-2">
              <h6 class="mb-0 fw-bold">
                <i class="bi bi-bell-fill me-2 text-primary"></i>Notifications
              </h6>
              <div class="d-flex align-items-center">
                @if($unreadCount > 0)
                <span class="badge bg-danger rounded-pill me-2">{{ $unreadCount }}</span>
                @endif
                <button class="btn btn-sm btn-outline-primary mark-all-read" title="Mark all as read">
                  <i class="bi bi-check2-all"></i>
                </button>
              </div>
            </div>
          </li>
          <li><hr class="dropdown-divider m-0"></li>
          <div class="notification-list" style="max-height: 300px; overflow-y: auto;">
            @forelse($notifications ?? [] as $notification)
            <li>
              <a class="dropdown-item notification-item {{ $notification['read'] ? '' : 'unread' }}" href="#" data-id="{{ $notification['id'] ?? '' }}">
                <div class="d-flex align-items-start">
                  <div class="notification-icon me-3">
                    <i class="bi {{ $notification['icon'] ?? 'bi-info-circle' }} text-{{ $notification['type'] ?? 'primary' }}"></i>
                  </div>
                  <div class="flex-grow-1">
                    <div class="notification-title fw-semibold">{{ $notification['title'] ?? 'Notification' }}</div>
                    <div class="notification-message text-muted small">{{ $notification['message'] ?? '' }}</div>
                    <div class="notification-time text-muted small">
                      <i class="bi bi-clock me-1"></i>{{ $notification['time'] ?? 'Just now' }}
                    </div>
                  </div>
                  @if(!($notification['read'] ?? false))
                  <div class="unread-indicator"></div>
                  @endif
                </div>
              </a>
            </li>
            @empty
            <li>
              <div class="text-center py-4 text-muted">
                <i class="bi bi-bell-slash fs-2 d-block mb-2"></i>
                <p class="mb-0">No notifications</p>
              </div>
            </li>
            @endforelse
          </div>
          @if(count($notifications ?? []) > 0)
          <li><hr class="dropdown-divider m-0"></li>
          <li>
            <a class="dropdown-item text-center text-primary fw-semibold" href="#">
              <i class="bi bi-eye me-1"></i>View All Notifications
            </a>
          </li>
          @endif
        </ul>
      </div>
      
      <button class="sidebar-toggle mobile-toggle" id="menu-btn" title="Open Menu">
        <i class="bi bi-list fs-5"></i>
      </button>
    </div>
  </div>
</nav>
