/**
 * Alert Helper Functions
 * Provides easy-to-use functions for showing alerts and toasts
 */

// Success Alert
function showSuccess(message, title = 'Berhasil!') {
    Swal.fire({
        title: title,
        text: message,
        icon: 'success',
        confirmButtonText: 'OK'
    });
}

// Error Alert
function showError(message, title = 'Error!') {
    Swal.fire({
        title: title,
        text: message,
        icon: 'error',
        confirmButtonText: 'OK'
    });
}

// Warning Alert
function showWarning(message, title = 'Peringatan!') {
    Swal.fire({
        title: title,
        text: message,
        icon: 'warning',
        confirmButtonText: 'OK'
    });
}

// Info Alert
function showInfo(message, title = 'Informasi') {
    Swal.fire({
        title: title,
        text: message,
        icon: 'info',
        confirmButtonText: 'OK'
    });
}

// Success Toast
function successToast(message, title = 'Berhasil!') {
    CustomAlert.toast({
        title: title,
        message: message,
        icon: 'success',
        duration: 3000
    });
}

// Error Toast
function errorToast(message, title = 'Error!') {
    CustomAlert.toast({
        title: title,
        message: message,
        icon: 'error',
        duration: 4000
    });
}

// Warning Toast
function warningToast(message, title = 'Peringatan!') {
    CustomAlert.toast({
        title: title,
        message: message,
        icon: 'warning',
        duration: 3500
    });
}

// Info Toast
function infoToast(message, title = 'Info') {
    CustomAlert.toast({
        title: title,
        message: message,
        icon: 'info',
        duration: 3000
    });
}

// Confirmation Dialog
function confirmAction(options = {}) {
    const {
        title = 'Apakah Anda yakin?',
        message = 'Tindakan ini tidak dapat dibatalkan.',
        confirmText = 'Ya, Lanjutkan',
        cancelText = 'Batal',
        icon = 'warning', // default icon
        onConfirm = () => {},
        onCancel = () => {}
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
    iconDiv.innerHTML = CustomAlert.prototype.getIconSVG(icon);

    // Create title
    const titleDiv = document.createElement('div');
    titleDiv.className = 'custom-alert-title';
    titleDiv.textContent = title;

    // Create message
    const messageDiv = document.createElement('div');
    messageDiv.className = 'custom-alert-message';
    messageDiv.textContent = message;

    // Create buttons container
    const buttonsContainer = document.createElement('div');
    buttonsContainer.className = 'custom-alert-buttons';

    // Create cancel button
    const cancelButton = document.createElement('button');
    cancelButton.className = 'custom-alert-button-cancel';
    cancelButton.innerHTML = `<span>${cancelText}</span>`;

    // Create confirm button
    const confirmButton = document.createElement('button');
    confirmButton.className = `custom-alert-button ${icon}`;
    confirmButton.innerHTML = `<span>${confirmText}</span>`;

    // Assemble
    buttonsContainer.appendChild(cancelButton);
    buttonsContainer.appendChild(confirmButton);

    container.appendChild(iconDiv);
    container.appendChild(titleDiv);
    container.appendChild(messageDiv);
    container.appendChild(buttonsContainer);
    overlay.appendChild(container);

    // Add to body
    document.body.appendChild(overlay);

    // Handle close
    const closeAlert = (callback) => {
        overlay.classList.add('fade-out');
        setTimeout(() => {
            document.body.removeChild(overlay);
            if (callback) callback();
        }, 200);
    };

    cancelButton.addEventListener('click', () => closeAlert(onCancel));
    confirmButton.addEventListener('click', () => closeAlert(onConfirm));

    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) closeAlert(onCancel);
    });

    // ESC key support
    const escapeHandler = (e) => {
        if (e.key === 'Escape') {
            closeAlert(onCancel);
            document.removeEventListener('keydown', escapeHandler);
        }
    };
    document.addEventListener('keydown', escapeHandler);
}

// Delete Confirmation (Specialized)
function confirmDelete(options = {}) {
    const {
        itemName = 'item ini',
        onConfirm = () => {},
        onCancel = () => {}
    } = options;

    confirmAction({
        title: 'Hapus Data?',
        message: `Anda yakin ingin menghapus ${itemName}? Tindakan ini tidak dapat dibatalkan.`,
        confirmText: 'Ya, Hapus',
        cancelText: 'Batal',
        icon: 'error', // Use red/error icon for delete
        onConfirm,
        onCancel
    });
}
