document.addEventListener('alpine:init', () => {
    Alpine.data('staffManagement', (routeTemplate) => ({
        deleteUrl: '',
        
        openDelete(staffId) {
            // Replace the placeholder with the actual staff ID
            this.deleteUrl = routeTemplate.replace('PLACEHOLDER', staffId);
            this.$dispatch('open-modal', 'delete-staff-modal');
        }
    }));
});
