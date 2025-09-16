    </div>
    
    <footer class="py-3 mt-5" style="background-color: var(--card-bg); border-top: 1px solid var(--border-color);">
        <div class="container text-center" style="color: var(--text-muted);">
            <small>&copy; <?php echo date('Y'); ?> Camp Of Coffee - Sales & Inventory System</small>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        // Initialize DataTables
        $(document).ready(function() {
            $('.datatable').DataTable({
                "pageLength": 10,
                "order": [[0, "desc"]]
            });
        });
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
        
        // Sidebar toggle functionality
        let isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        const toggleIcon = document.getElementById('toggle-icon');
        
        // Initialize sidebar state
        if (isCollapsed) {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('expanded');
            toggleIcon.classList.replace('bx-menu', 'bx-menu-alt-right');
        }
        
        function toggleSidebar() {
            isCollapsed = !isCollapsed;
            
            if (window.innerWidth <= 768) {
                // Mobile behavior
                sidebar.classList.toggle('show');
            } else {
                // Desktop behavior
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
                
                if (isCollapsed) {
                    toggleIcon.classList.replace('bx-menu', 'bx-menu-alt-right');
                } else {
                    toggleIcon.classList.replace('bx-menu-alt-right', 'bx-menu');
                }
            }
            
            // Save state to localStorage
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        }
        
        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                sidebar.classList.remove('show');
                if (isCollapsed) {
                    sidebar.classList.add('collapsed');
                    mainContent.classList.add('expanded');
                } else {
                    sidebar.classList.remove('collapsed');
                    mainContent.classList.remove('expanded');
                }
            } else {
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('expanded');
            }
        });
        
        // Close sidebar on mobile when clicking outside
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 768) {
                const target = event.target;
                const sidebar = document.getElementById('sidebar');
                const mobileToggle = document.querySelector('.mobile-toggle');
                
                if (!sidebar.contains(target) && !mobileToggle.contains(target)) {
                    sidebar.classList.remove('show');
                }
            }
        });
        
        // Theme toggle functionality
        let currentTheme = localStorage.getItem('theme') || 'light';
        const body = document.body;
        const themeIcon = document.getElementById('theme-icon');
        
        // Initialize theme
        function initializeTheme() {
            if (currentTheme === 'dark') {
                body.setAttribute('data-theme', 'dark');
                themeIcon.classList.replace('bx-moon', 'bx-sun');
            } else {
                body.removeAttribute('data-theme');
                themeIcon.classList.replace('bx-sun', 'bx-moon');
            }
        }
        
        // Toggle theme
        function toggleTheme() {
            currentTheme = currentTheme === 'light' ? 'dark' : 'light';
            localStorage.setItem('theme', currentTheme);
            
            if (currentTheme === 'dark') {
                body.setAttribute('data-theme', 'dark');
                themeIcon.classList.replace('bx-moon', 'bx-sun');
            } else {
                body.removeAttribute('data-theme');
                themeIcon.classList.replace('bx-sun', 'bx-moon');
            }
        }
        
        // Initialize theme on page load
        initializeTheme();
    </script>
</body>
</html>
