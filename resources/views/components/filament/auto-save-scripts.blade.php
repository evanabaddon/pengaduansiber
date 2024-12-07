<script>
    // Inisialisasi auto-save
    window.addEventListener('init-auto-save', (event) => {
        console.log('Auto-save initialized with interval:', event.detail.interval);
        
        let autoSaveInterval = setInterval(() => {
            if (document.visibilityState === 'visible') {
                console.log('Triggering auto-save...');
                try {
                    Livewire.dispatch('autoSaveDraft');
                    console.log('Auto-save dispatch sent');
                } catch (error) {
                    console.error('Auto-save error:', error);
                }
            }
        }, event.detail.interval);

        window.addEventListener('beforeunload', () => {
            clearInterval(autoSaveInterval);
        });
    });

    // Gunakan Filament notifications API
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('draft-saved', (data) => {
            window.$wireui.notify({
                title: 'Berhasil',
                description: data.message,
                icon: 'success'
            });
        });

        Livewire.on('draft-save-failed', (data) => {
            window.$wireui.notify({
                title: 'Error',
                description: data.message,
                icon: 'error'
            });
        });
    });
</script>