document.addEventListener('DOMContentLoaded', function() {
    
    /* =========================================
       1. GESTIONE SIDEBAR (Navigazione Tab)
       ========================================= */
    const navButtons = document.querySelectorAll('.nav-btn');
    const contentPanels = document.querySelectorAll('.content-panel');

    if (navButtons.length > 0) {
        navButtons.forEach(button => {
            button.addEventListener('click', () => {
                navButtons.forEach(btn => btn.classList.remove('active'));
                contentPanels.forEach(panel => panel.classList.remove('active'));
                button.classList.add('active');
                const targetId = button.getAttribute('data-target');
                const targetPanel = document.getElementById(targetId);

                if (targetPanel) {
                    targetPanel.classList.add('active');
                }
            });
        });
    }

    /* =========================================
       2. GESTIONE FILTRI ABBONAMENTI (Admin)
       ========================================= */
    const filterButtons = document.querySelectorAll('.filter-tag');
    const subscriptionItems = document.querySelectorAll('#admin-abbonamenti .list-item');

    if (filterButtons.length > 0) {
        filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                filterButtons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');

                const filterValue = button.getAttribute('data-filter');

                subscriptionItems.forEach(item => {
                    const status = item.getAttribute('data-status');

                    if (filterValue === 'all' || filterValue === status) {
                        item.style.display = 'flex'; 
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
    }

});