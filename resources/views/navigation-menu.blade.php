<div class="nav-wrapper">
    <nav class="navbar navbar-expand-lg" style="background: linear-gradient(to right, #ffffff, #f8fdff); border-bottom: 2px solid #e6f4ff; margin-bottom: 20px; padding: 15px 0;">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('/') }}">
                <span class="brand-text" style="color: #333; font-size: 24px; font-weight: 600; letter-spacing: 0.5px;">ARKAN</span>
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
                                <i class="fas fa-bell notification-bell"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge" style="display: none;">
                                    0
                                </span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end notification-dropdown shadow-lg py-0" style="width: 320px; max-height: 400px; overflow-y: auto; border: none; border-radius: 12px;">
                                <div class="notification-header border-bottom p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 text-primary">Notifications</h6>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input" id="toggleSound" checked>
                                            <label class="form-check-label small" for="toggleSound">Sound</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="notifications-container">
                                    <div class="text-center p-4 text-muted">
                                        <i class="fas fa-bell-slash mb-3" style="font-size: 24px; opacity: 0.5;"></i>
                                        <p class="mb-0 small">No notifications available</p>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <!-- User Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg" style="border: none; border-radius: 12px;" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item py-2" href="/user/profile">Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item py-2 text-danger">Logout</button>
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

    <style>
    .navbar {
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.04);
    }

    .nav-link {
        color: #555 !important;
        font-weight: 500;
        padding: 0.5rem 1rem;
        transition: all 0.3s ease;
        border-radius: 6px;
        margin: 0 3px;
    }

    .nav-link:hover, .nav-link.active {
        color: #2196F3 !important;
        background-color: #e3f2fd;
    }

    .notification-bell {
        color: #2196F3;
        font-size: 1.2rem;
        transition: transform 0.3s ease;
    }

    .nav-link:hover .notification-bell {
        transform: rotate(15deg);
    }

    .notification-badge {
        font-size: 0.65rem;
        padding: 0.25em 0.6em;
        transform: translate(40%, -40%) !important;
        box-shadow: 0 2px 4px rgba(220, 53, 69, 0.2);
    }

    .notification-item {
        padding: 12px 16px;
        border-bottom: 1px solid #eef2f7;
        transition: all 0.3s ease;
    }

    .notification-item:hover {
        background-color: #f8fdff;
    }

    .notification-item.unread {
        background-color: #e3f2fd;
    }

    .notification-content {
        margin-bottom: 5px;
        color: #333;
    }

    .notification-time {
        font-size: 0.8rem;
        color: #78909c;
    }

    .dropdown-menu {
        animation: dropdownFade 0.2s ease-in-out;
    }

    .dropdown-item {
        transition: all 0.2s ease;
    }

    .dropdown-item:hover {
        background-color: #e3f2fd;
    }

    @keyframes dropdownFade {
        from {
            opacity: 0;
            transform: translateY(-8px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes bellShake {
        0% { transform: rotate(0); }
        15% { transform: rotate(15deg); }
        30% { transform: rotate(-15deg); }
        45% { transform: rotate(10deg); }
        60% { transform: rotate(-10deg); }
        75% { transform: rotate(5deg); }
        85% { transform: rotate(-5deg); }
        100% { transform: rotate(0); }
    }

    .shake {
        animation: bellShake 0.8s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
    }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let lastNotificationId = null;
        const bellIcon = document.querySelector('.notification-bell');

        const playNotificationSound = () => {
            if (document.querySelector('#toggleSound').checked) {
                const audio = new Audio('/sounds/notification-18-270129.mp3');
                audio.volume = 0.5;
                audio.play();
                bellIcon.classList.add('shake');
                setTimeout(() => bellIcon.classList.remove('shake'), 1000);
            }
        };

        const updateNotifications = () => {
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
                })
                .catch(error => console.error('Error updating unread count:', error));

            fetch('/notifications')
                .then(response => response.text())
                .then(html => {
                    const container = document.querySelector('.notifications-container');
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = html;

                    const latestNotification = tempDiv.querySelector('.notification-item');
                    if (latestNotification) {
                        const latestNotificationId = latestNotification.dataset.id;
                        if (lastNotificationId !== null && lastNotificationId !== latestNotificationId) {
                            playNotificationSound();
                        }
                        lastNotificationId = latestNotificationId;
                    }

                    if (html.trim()) {
                        container.innerHTML = html;
                    } else {
                        container.innerHTML = `
                            <div class="text-center p-4 text-muted">
                                <i class="fas fa-bell-slash mb-3" style="font-size: 24px; opacity: 0.5;"></i>
                                <p class="mb-0 small">No notifications available</p>
                            </div>
                        `;
                    }
                })
                .catch(error => console.error('Error updating notifications:', error));
        };

        updateNotifications();
        setInterval(updateNotifications, 30000);

        document.querySelector('.notifications-container').addEventListener('click', function(e) {
            const notificationLink = e.target.closest('.notification-link');
            if (notificationLink) {
                e.preventDefault();
                fetch(notificationLink.dataset.markAsRead)
                    .then(response => response.json())
                    .then(() => {
                        window.location.href = notificationLink.href;
                    })
                    .catch(error => console.error('Error marking notification as read:', error));
            }
        });

        document.querySelector('#toggleSound').addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
    </script>
</div>
