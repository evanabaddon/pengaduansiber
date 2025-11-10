document.addEventListener('DOMContentLoaded', function() {
    // Handle dropdown navigation items
    document.querySelectorAll('.custom-dropdown-item').forEach(item => {
        const link = item.querySelector('a');
        const badge = item.querySelector('.fi-sidebar-item-badge');
        
        if (link && badge) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const isOpen = item.classList.contains('open');
                
                // Close all other dropdowns in the same group
                const group = item.closest('.fi-sidebar-group');
                if (group) {
                    group.querySelectorAll('.custom-dropdown-item.open').forEach(dropdown => {
                        if (dropdown !== item) {
                            dropdown.classList.remove('open');
                        }
                    });
                }
                
                // Toggle current dropdown
                item.classList.toggle('open', !isOpen);
                
                // Create or remove submenu
                let submenu = item.querySelector('.dropdown-submenu');
                if (!isOpen && !submenu) {
                    const dropdownData = JSON.parse(item.getAttribute('data-dropdown-items') || '[]');
                    submenu = createSubmenu(dropdownData);
                    item.appendChild(submenu);
                } else if (isOpen && submenu) {
                    submenu.remove();
                }
            });
        }
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.custom-dropdown-item')) {
            document.querySelectorAll('.custom-dropdown-item.open').forEach(item => {
                item.classList.remove('open');
                const submenu = item.querySelector('.dropdown-submenu');
                if (submenu) {
                    submenu.remove();
                }
            });
        }
    });

    // Auto-open active dropdowns
    document.querySelectorAll('.custom-dropdown-item').forEach(item => {
        const dropdownData = JSON.parse(item.getAttribute('data-dropdown-items') || '[]');
        const hasActiveChild = dropdownData.some(item => item.active);
        
        if (hasActiveChild) {
            item.classList.add('open');
            const submenu = createSubmenu(dropdownData);
            item.appendChild(submenu);
        }
    });

    function createSubmenu(items) {
        const submenu = document.createElement('div');
        submenu.className = 'dropdown-submenu';
        
        items.forEach(item => {
            const subitem = document.createElement('a');
            subitem.href = item.url;
            subitem.className = `dropdown-subitem ${item.active ? 'active' : ''}`;
            subitem.textContent = item.label;
            submenu.appendChild(subitem);
        });
        
        return submenu;
    }
});