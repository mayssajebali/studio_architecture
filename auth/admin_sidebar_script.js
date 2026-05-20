// Sidebar toggle functionality
let collapsed = false;

function toggleSidebar() {
    collapsed = !collapsed;
    document.getElementById('sidebar').classList.toggle('collapsed', collapsed);
    const chevron = document.getElementById('toggle-chevron');
    if (chevron) {
        chevron.className = collapsed ? 'bi bi-chevron-right' : 'bi bi-chevron-left';
    }
}

function toggleMobileSidebar() {
    document.getElementById('sidebar').classList.toggle('mobile-open');
}

// Close mobile sidebar when clicking outside
document.addEventListener('click', function(e) {
    const sidebar = document.getElementById('sidebar');
    const mobileToggle = document.querySelector('.mobile-toggle');
    
    if (window.innerWidth <= 768 && sidebar && sidebar.classList.contains('mobile-open')) {
        if (!sidebar.contains(e.target) && (!mobileToggle || !mobileToggle.contains(e.target))) {
            sidebar.classList.remove('mobile-open');
        }
    }
});
