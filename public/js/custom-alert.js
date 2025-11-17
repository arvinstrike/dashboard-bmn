/**
 * Custom Alert System - Professional & Modern
 * Version: 1.0.0
 * A lightweight, modern alert system to replace SweetAlert2
 */

class CustomAlert {
    constructor() {
        this.queue = [];
        this.isShowing = false;
    }

    /**
     * Show alert modal
     * @param {Object} options - Alert options
     * @param {String} options.title - Alert title
     * @param {String} options.text - Alert message
     * @param {String} options.icon - Alert type (success, error, warning, info)
     * @param {String} options.confirmButtonText - Button text (default: 'OK')
     * @param {Function} options.onConfirm - Callback after button click
     */
    fire(options = {}) {
        const {
            title = 'Notification',
            text = '',
            icon = 'info',
            confirmButtonText = 'OK',
            onConfirm = null
        } = options;

        // Create overlay
        const overlay = document.createElement('div');
        overlay.className = 'custom-alert-overlay';

        // Create container
        const container = document.createElement('div');
        container.className = 'custom-alert-container';

        // Create icon
        const iconDiv = document.createElement('div');
        iconDiv.className = `custom-alert-icon ${icon}`;
        iconDiv.innerHTML = this.getIconSVG(icon);

        // Create title
        const titleDiv = document.createElement('div');
        titleDiv.className = 'custom-alert-title';
        titleDiv.textContent = title;

        // Create message
        const messageDiv = document.createElement('div');
        messageDiv.className = 'custom-alert-message';
        messageDiv.textContent = text;

        // Create button
        const button = document.createElement('button');
        button.className = `custom-alert-button ${icon}`;
        button.innerHTML = `<span>${confirmButtonText}</span>`;

        // Assemble
        container.appendChild(iconDiv);
        container.appendChild(titleDiv);
        container.appendChild(messageDiv);
        container.appendChild(button);
        overlay.appendChild(container);

        // Add to body
        document.body.appendChild(overlay);

        // Handle close
        const closeAlert = () => {
            overlay.classList.add('fade-out');
            setTimeout(() => {
                document.body.removeChild(overlay);
                if (onConfirm) onConfirm();
            }, 200);
        };

        button.addEventListener('click', closeAlert);
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) closeAlert();
        });

        // ESC key support
        const escapeHandler = (e) => {
            if (e.key === 'Escape') {
                closeAlert();
                document.removeEventListener('keydown', escapeHandler);
            }
        };
        document.addEventListener('keydown', escapeHandler);

        return {
            close: closeAlert
        };
    }

    /**
     * Show success alert
     */
    success(title, text, confirmButtonText = 'OK') {
        return this.fire({
            title,
            text,
            icon: 'success',
            confirmButtonText
        });
    }

    /**
     * Show error alert
     */
    error(title, text, confirmButtonText = 'OK') {
        return this.fire({
            title,
            text,
            icon: 'error',
            confirmButtonText
        });
    }

    /**
     * Show warning alert
     */
    warning(title, text, confirmButtonText = 'OK') {
        return this.fire({
            title,
            text,
            icon: 'warning',
            confirmButtonText
        });
    }

    /**
     * Show info alert
     */
    info(title, text, confirmButtonText = 'OK') {
        return this.fire({
            title,
            text,
            icon: 'info',
            confirmButtonText
        });
    }

    /**
     * Show toast notification
     * @param {Object} options - Toast options
     * @param {String} options.title - Toast title
     * @param {String} options.message - Toast message
     * @param {String} options.icon - Toast type (success, error, warning, info)
     * @param {Number} options.duration - Duration in ms (default: 3000)
     */
    toast(options = {}) {
        const {
            title = 'Notification',
            message = '',
            icon = 'info',
            duration = 3000
        } = options;

        // Create toast
        const toast = document.createElement('div');
        toast.className = 'custom-alert-toast';

        // Create icon
        const iconDiv = document.createElement('div');
        iconDiv.className = `custom-alert-toast-icon ${icon}`;
        iconDiv.style.background = this.getIconColor(icon);
        iconDiv.innerHTML = this.getIconSVG(icon);

        // Create content
        const content = document.createElement('div');
        content.className = 'custom-alert-toast-content';

        const titleDiv = document.createElement('div');
        titleDiv.className = 'custom-alert-toast-title';
        titleDiv.textContent = title;

        const messageDiv = document.createElement('div');
        messageDiv.className = 'custom-alert-toast-message';
        messageDiv.textContent = message;

        content.appendChild(titleDiv);
        content.appendChild(messageDiv);

        // Assemble
        toast.appendChild(iconDiv);
        toast.appendChild(content);

        // Add to body
        document.body.appendChild(toast);

        // Auto remove
        setTimeout(() => {
            toast.classList.add('fade-out');
            setTimeout(() => {
                if (document.body.contains(toast)) {
                    document.body.removeChild(toast);
                }
            }, 300);
        }, duration);

        // Click to dismiss
        toast.addEventListener('click', () => {
            toast.classList.add('fade-out');
            setTimeout(() => {
                if (document.body.contains(toast)) {
                    document.body.removeChild(toast);
                }
            }, 300);
        });
    }

    /**
     * Get icon SVG based on type
     */
    getIconSVG(type) {
        const icons = {
            success: `
                <svg viewBox="0 0 52 52">
                    <circle cx="26" cy="26" r="25" fill="none"/>
                    <path fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                </svg>
            `,
            error: `
                <svg viewBox="0 0 52 52">
                    <circle cx="26" cy="26" r="25" fill="none"/>
                    <path fill="none" d="M16 16 36 36 M36 16 16 36"/>
                </svg>
            `,
            warning: `
                <svg viewBox="0 0 52 52">
                    <path fill="none" d="M26 16v12M26 34v2"/>
                    <circle cx="26" cy="26" r="24" fill="none"/>
                </svg>
            `,
            info: `
                <svg viewBox="0 0 52 52">
                    <circle cx="26" cy="26" r="24" fill="none"/>
                    <path fill="none" d="M26 20v16M26 14v2"/>
                </svg>
            `
        };
        return icons[type] || icons.info;
    }

    /**
     * Get icon color based on type
     */
    getIconColor(type) {
        const colors = {
            success: 'linear-gradient(135deg, #10b981 0%, #059669 100%)',
            error: 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)',
            warning: 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)',
            info: 'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)'
        };
        return colors[type] || colors.info;
    }
}

// Create global instance
window.CustomAlert = new CustomAlert();

// Create Swal alias for backward compatibility
window.Swal = {
    fire: (options) => {
        // Handle different parameter formats
        if (typeof options === 'string') {
            // Swal.fire('Title', 'Message', 'icon')
            const title = arguments[0] || 'Notification';
            const text = arguments[1] || '';
            const icon = arguments[2] || 'info';
            return window.CustomAlert.fire({ title, text, icon });
        } else {
            // Swal.fire({ ... })
            return window.CustomAlert.fire(options);
        }
    },

    // Shorthand methods
    success: (title, text) => window.CustomAlert.success(title, text),
    error: (title, text) => window.CustomAlert.error(title, text),
    warning: (title, text) => window.CustomAlert.warning(title, text),
    info: (title, text) => window.CustomAlert.info(title, text)
};

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = CustomAlert;
}
