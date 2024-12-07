// Inisialisasi auto-save
window.addEventListener('init-auto-save', (event) => {
    console.log('Auto-save initialized with interval:', event.detail.interval);
    
    let autoSaveInterval = setInterval(() => {
        if (document.visibilityState === 'visible') {
            console.log('Triggering auto-save...'); // Debug log
            Livewire.emit('autoSaveDraft');
        }
    }, event.detail.interval);

    // Cleanup interval saat komponen dihapus
    Livewire.on('destroyComponent', () => {
        console.log('Cleaning up auto-save interval'); // Debug log
        clearInterval(autoSaveInterval);
    });
});

// Notifikasi ketika draft disimpan
window.addEventListener('draft-saved', (event) => {
    // Gunakan sistem notifikasi Filament
    Filament.notify('success', event.detail.message, {
        position: 'bottom-right',
        duration: 2000,
    });
});

// Hapus draft dari storage
window.addEventListener('clear-draft-storage', () => {
    localStorage.removeItem('laporan_informasi_draft');
});

// Konfirmasi sebelum meninggalkan halaman
window.addEventListener('beforeunload', (event) => {
    if (document.querySelector('[wire\\:dirty]')) {
        event.preventDefault();
        event.returnValue = '';
    }
}); 