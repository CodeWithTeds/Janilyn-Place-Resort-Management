document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('submit', (event) => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement)) return;
        if (!form.classList.contains('check-in-form')) return;

        if (form.dataset.confirmed === 'true') {
            return;
        }

        const unitSelect = form.querySelector('#check_in_unit_id');
        const needsUnit = !!unitSelect && unitSelect.offsetParent !== null;
        const hasNoUnits = needsUnit && unitSelect.options.length <= 1;
        const hasNoSelection = needsUnit && !unitSelect.value;

        if (hasNoUnits) {
            event.preventDefault();
            event.stopImmediatePropagation();
            if (window.Swal) {
                window.Swal.fire({
                    icon: 'warning',
                    title: 'Check-in Validation',
                    text: 'No available units found for this booking dates.'
                });
            } else {
                alert('Check-in Validation: No available units found for this booking dates.');
            }
            return;
        }

        if (hasNoSelection) {
            event.preventDefault();
            event.stopImmediatePropagation();
            if (window.Swal) {
                window.Swal.fire({
                    icon: 'warning',
                    title: 'Check-in Validation',
                    text: 'Please select a unit before confirming check-in.'
                });
            } else {
                alert('Check-in Validation: Please select a unit before confirming check-in.');
            }
            return;
        }

        event.preventDefault();
        event.stopImmediatePropagation();

        if (window.Swal) {
            window.Swal.fire({
                icon: 'question',
                title: 'Check In Guest?',
                text: 'Are you sure you want to check in this guest?',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, check in!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.dataset.confirmed = 'true';
                    form.submit();
                }
            });
            return;
        }

        form.dataset.confirmed = 'true';
        form.submit();
    });
});
