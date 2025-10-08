{{-- Toast Notification Component (Success & Error only) --}}
<!-- Toast Container -->
<div id="toast-container" class="toast-container"></div>

<style>
        /* Toast Notification Styles */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 400px;
        }

        .toast-notification {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            padding: 16px 20px;
            border-left: 4px solid;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            transform: translateX(450px);
            transition: all 0.3s ease;
            opacity: 0;
            min-width: 300px;
        }

        .toast-notification.show {
            transform: translateX(0);
            opacity: 1;
        }

        .toast-notification.hide {
            transform: translateX(450px);
            opacity: 0;
        }

        .toast-success {
            border-left-color: #22c55e;
        }

        .toast-error {
            border-left-color: #ef4444;
        }

        .toast-icon {
            flex-shrink: 0;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: white;
            margin-top: 2px;
        }

        .toast-success .toast-icon {
            background: #22c55e;
        }

        .toast-error .toast-icon {
            background: #ef4444;
        }

        .toast-content {
            flex: 1;
        }

        .toast-title {
            font-weight: 600;
            font-size: 14px;
            color: #1f2937;
            margin-bottom: 4px;
        }

        .toast-message {
            font-size: 13px;
            color: #6b7280;
            line-height: 1.4;
        }

        .toast-message ul {
            margin: 0;
            padding-left: 16px;
        }

        .toast-message li {
            margin-bottom: 2px;
        }

        .toast-close {
            flex-shrink: 0;
            background: none;
            border: none;
            color: #9ca3af;
            font-size: 18px;
            cursor: pointer;
            padding: 0;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s ease;
        }

        .toast-close:hover {
            color: #6b7280;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .toast-container {
                top: 10px;
                right: 10px;
                left: 10px;
                max-width: none;
            }

            .toast-notification {
                min-width: auto;
            }
        }
    </style>

@push('scripts')
    <script>
        // Toast Notification Functions (Success & Error only)
        function showToast(type, title, message, duration = 2000) {
            const container = document.getElementById('toast-container');
            if (!container) {
                console.error('Toast container not found');
                return;
            }

            const toastId = 'toast-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);

            // Icon based on type
            let icon = '';
            switch (type) {
                case 'success':
                    icon = '<i class="fas fa-check"></i>';
                    break;
                case 'error':
                    icon = '<i class="fas fa-exclamation-triangle"></i>';
                    break;
                default:
                    icon = '<i class="fas fa-bell"></i>';
            }

            const toastHtml = `
            <div id="${toastId}" class="toast-notification toast-${type}">
                <div class="toast-icon">
                    ${icon}
                </div>
                <div class="toast-content">
                    <div class="toast-title">${title}</div>
                    <div class="toast-message">${message}</div>
                </div>
                <button class="toast-close" onclick="hideToast('${toastId}')">&times;</button>
            </div>
        `;

            container.insertAdjacentHTML('beforeend', toastHtml);

            const toast = document.getElementById(toastId);

            // Show toast with animation
            setTimeout(() => {
                toast.classList.add('show');
            }, 100);

            // Auto hide after specified duration
            setTimeout(() => {
                hideToast(toastId);
            }, duration);
        }

        function hideToast(toastId) {
            const toast = document.getElementById(toastId);
            if (toast) {
                toast.classList.remove('show');
                toast.classList.add('hide');
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }
        }

        // Show Laravel session messages
        function showLaravelMessages() {
            @if (session('success'))
                setTimeout(() => {
                    showToast('success', 'Thành công!', '{{ session('success') }}');
                }, 500);
            @endif

            @if (session('error'))
                setTimeout(() => {
                    showToast('error', 'Lỗi!', '{{ session('error') }}');
                }, 500);
            @endif

            // Only Success & Error retained

            @if ($errors->any())
                setTimeout(() => {
                    let errorMessage = '<ul>';
                    @foreach ($errors->all() as $error)
                        errorMessage += '<li>{{ $error }}</li>';
                    @endforeach
                    errorMessage += '</ul>';
                    showToast('error', 'Có lỗi xảy ra!', errorMessage);
                }, 500);
            @endif
        }

        // Auto-show messages when page loads
        document.addEventListener('DOMContentLoaded', function() {
            showLaravelMessages();
        });

        // Global toast functions for easy access
        window.showSuccessToast = function(title, message, duration = 2000) {
            showToast('success', title, message, duration);
        };

        window.showErrorToast = function(title, message, duration = 2000) {
            showToast('error', title, message, duration);
        };

        // Removed other variants per product decision
    </script>
@endpush
