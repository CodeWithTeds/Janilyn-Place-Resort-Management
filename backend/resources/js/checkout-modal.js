import Swal from 'sweetalert2';

document.addEventListener('DOMContentLoaded', () => {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

  document.addEventListener('submit', async (e) => {
    const form = e.target;
    if (!(form instanceof HTMLFormElement)) return;
    if (!form.classList.contains('check-out-form')) return;
    if (form.dataset.confirmed === 'true') return;

    e.preventDefault();
    e.stopImmediatePropagation();

    const result = await Swal.fire({
      title: 'Check-out Options',
      text: 'Proceed to check out or create an incident report?',
      icon: 'question',
      showDenyButton: true,
      showCancelButton: true,
      confirmButtonText: 'Check-Out',
      denyButtonText: 'Create Incident Report',
      cancelButtonText: 'Cancel',
    });

    if (result.isConfirmed) {
      form.dataset.confirmed = 'true';
      form.submit();
      return;
    }

    if (result.isDenied) {
      const url = form.dataset.unitStatusUrl;
      if (!url) {
        await Swal.fire('No Unit', 'This booking has no assigned unit.', 'info');
        return;
      }
      try {
        const response = await fetch(url, {
          method: 'PATCH',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
            'X-CSRF-TOKEN': csrf,
          },
          body: new URLSearchParams({ cleaning_status: 'dirty' }),
        });
        if (response.ok) {
          await Swal.fire('Marked Dirty', 'Unit status set to Dirty.', 'success');
        } else {
          await Swal.fire('Update Failed', 'Could not update unit status.', 'error');
        }
      } catch (err) {
        await Swal.fire('Network Error', 'Please try again.', 'error');
      }
    }
  }, { capture: true });
});

