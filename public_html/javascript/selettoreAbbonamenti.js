
document.addEventListener('DOMContentLoaded', function() {
    /* =========================================
       1. GESTIONE SIDEBAR (Navigazione Tab)
       ========================================= */
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabPanels = document.querySelectorAll('.tab-panel');

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            
            tabButtons.forEach(btn => btn.classList.remove('active'));
            
            tabPanels.forEach(panel => panel.classList.remove('active'));

            button.classList.add('active');

            const targetId = button.getAttribute('data-target');
            const targetPanel = document.getElementById(targetId);

            if (targetPanel) {
                targetPanel.classList.add('active');
            }
        });
    });
});