import './bootstrap';
import './housekeeping-staff';
import './checkin-sweet-alert';
import Swal from 'sweetalert2';

window.Swal = Swal;

document.addEventListener('DOMContentLoaded', () => {
    // 1. Handle Flash Messages
    const flashMessages = document.getElementById('flash-messages');
    if (flashMessages) {
        const success = flashMessages.dataset.success;
        const error = flashMessages.dataset.error;
        const warning = flashMessages.dataset.warning;
        const info = flashMessages.dataset.info;

        if (success) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: success,
                timer: 3000,
                showConfirmButton: false
            });
        }
        if (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error,
            });
        }
        if (warning) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning',
                text: warning,
            });
        }
        if (info) {
            Swal.fire({
                icon: 'info',
                title: 'Info',
                text: info,
            });
        }
    }

    // 2. Handle Confirm Actions via Event Delegation
    document.addEventListener('submit', function (e) {
        const form = e.target;
        
        // Check if the form has the 'confirm-action' class
        if (form.classList.contains('confirm-action')) {
            // Check if we have already confirmed this submission
            if (form.dataset.confirmed === 'true') {
                return; // Allow submission
            }

            e.preventDefault(); // Stop submission

            const title = form.dataset.confirmTitle || 'Are you sure?';
            const text = form.dataset.confirmText || "You won't be able to revert this!";
            const icon = form.dataset.confirmIcon || 'warning';
            const confirmButtonText = form.dataset.confirmButtonText || 'Yes, do it!';

            Swal.fire({
                title: title,
                text: text,
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: confirmButtonText
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mark as confirmed and submit
                    form.dataset.confirmed = 'true';
                    form.submit();
                }
            });
        }
    });
});
