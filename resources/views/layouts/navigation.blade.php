<nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="margin-bottom:20px">
     <div class="container">
         <a class="navbar-brand" href="{{ route('/') }}">
             <span class="brand-text">ARKAN</span>
         </a>
         <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
             aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
             <span class="navbar-toggler-icon"></span>
         </button>

         <div class="collapse navbar-collapse" id="navbarNav">
             <ul class="navbar-nav ms-auto">
                 <li class="nav-item">
                     <a class="nav-link {{ request()->routeIs('/') ? 'active' : '' }}" href="{{ route('/') }}">Home</a>
                 </li>

                 @auth
                     @if(auth()->user()->role == 'manager')
                         <li class="nav-item">
                             <a class="nav-link {{ request()->routeIs('attendances.index') ? 'active' : '' }}" href="{{ route('attendances.index') }}">Attendance Records</a>
                         </li>
                         <li class="nav-item">
                             <a class="nav-link {{ request()->routeIs('leaves.index') ? 'active' : '' }}" href="{{ route('leaves.index') }}">Leave Records</a>
                         </li>
                     @endif

                     @if(auth()->user()->role == 'employee')
                         <li class="nav-item">
                             <a class="nav-link {{ request()->routeIs('attendances.create') ? 'active' : '' }}" href="{{ route('attendances.create') }}">Mark Attendance</a>
                         </li>
                         <li class="nav-item">
                             <a class="nav-link {{ request()->routeIs('leaves.create') ? 'active' : '' }}" href="{{ route('leaves.create') }}">Mark Leave</a>
                         </li>
                     @endif

                     <!-- Notification Bell -->
                     <li class="nav-item dropdown">
                         <a class="nav-link position-relative" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                             <i class="fas fa-bell"></i>
                             <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge" style="display: none;">
                                 0
                             </span>
                         </a>
                         <div class="dropdown-menu dropdown-menu-end notification-dropdown py-0" style="width: 300px; max-height: 400px; overflow-y: auto;">
                             <div class="border-bottom p-2">
                                 <h6 class="mb-0">Notifications</h6>
                             </div>
                             <div class="notifications-container">
                                 <!-- Notifications will be loaded here -->
                             </div>
                         </div>
                     </li>

                     <!-- User Dropdown -->
                     <li class="nav-item dropdown">
                         <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                             data-bs-toggle="dropdown" aria-expanded="false">
                             {{ Auth::user()->name }}
                         </a>
                         <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                             <li><a class="dropdown-item" href="/user/profile">Profile</a></li>
                             <li><hr class="dropdown-divider"></li>
                             <li>
                                 <form method="POST" action="{{ route('logout') }}">
                                     @csrf
                                     <button type="submit" class="dropdown-item">Logout</button>
                                 </form>
                             </li>
                         </ul>
                     </li>
                 @else
                     <li class="nav-item">
                         <a class="nav-link" href="{{ route('login') }}">Login</a>
                     </li>
                 @endauth
             </ul>
         </div>
     </div>
</nav>

@push('styles')
<style>
.notification-item {
    padding: 10px;
    border-bottom: 1px solid #eee;
    transition: background-color 0.3s;
}

.notification-item:hover {
    background-color: #f8f9fa;
}

.notification-item.unread {
    background-color: #f0f7ff;
}

.notification-content {
    margin-bottom: 5px;
}

.notification-time {
    font-size: 0.8rem;
    color: #6c757d;
}

.notification-badge {
    font-size: 0.6rem;
    transform: translate(50%, -50%) !important;
}

.dropdown-menu {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const updateNotifications = () => {
        // Update unread count
        fetch('/notifications/unread-count')
            .then(response => response.json())
            .then(data => {
                const badge = document.querySelector('.notification-badge');
                if (data.count > 0) {
                    badge.style.display = 'inline';
                    badge.textContent = data.count;
                } else {
                    badge.style.display = 'none';
                }
            });

        // Update notification list
        fetch('/notifications')
            .then(response => response.text())
            .then(html => {
                document.querySelector('.notifications-container').innerHTML = html;
            });
    };

    // Update notifications every 30 seconds
    updateNotifications();
    setInterval(updateNotifications, 30000);

    // Mark notification as read when clicked
    document.querySelector('.notifications-container').addEventListener('click', function(e) {
        const notificationLink = e.target.closest('.notification-link');
        if (notificationLink) {
            e.preventDefault();
            fetch(notificationLink.dataset.markAsRead)
                .then(response => response.json())
                .then(() => {
                
                    window.location.href = notificationLink.href;
                });
        }
    });
});
</script>
@endpush
