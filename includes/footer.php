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
        
        // AJAX Navigation System
        let isLoading = false;
        
        // Initialize AJAX navigation
        function initializeAjaxNavigation() {
            // Add click handlers to navigation links and quick action buttons
            const selectors = '.nav-link, .btn[href], a[href*=".php"]:not([target="_blank"]):not(.no-ajax)';
            document.querySelectorAll(selectors).forEach(link => {
                // Skip external links and logout
                const href = link.getAttribute('href');
                if (!href || href.includes('logout.php') || href.startsWith('http') || href.startsWith('mailto:')) {
                    return;
                }
                
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const href = this.getAttribute('href');
                    
                    // Don't load if it's the current page
                    if (href === window.location.pathname.split('/').pop()) {
                        return;
                    }
                    
                    loadPage(href, this);
                });
            });
            
            // Handle browser back/forward buttons
            window.addEventListener('popstate', function(e) {
                if (e.state && e.state.page) {
                    loadPage(e.state.page, null, false);
                }
            });
        }
        
        // Load page content via AJAX
        function loadPage(url, clickedLink = null, pushState = true) {
            if (isLoading) return;
            
            isLoading = true;
            showLoadingState();
            
            // Update active navigation
            if (clickedLink) {
                updateActiveNavigation(clickedLink);
            }
            
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text();
                })
                .then(html => {
                    // Extract main content from the response
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContent = doc.querySelector('.main-content');
                    const newTitle = doc.querySelector('title');
                    
                    if (newContent) {
                        // Update page content with smooth transition
                        const mainContent = document.querySelector('.main-content');
                        
                        // Fade out current content
                        mainContent.style.opacity = '0';
                        mainContent.style.transform = 'translateY(20px)';
                        
                        setTimeout(() => {
                            // Replace content
                            mainContent.innerHTML = newContent.innerHTML;
                            
                            // Update page title
                            if (newTitle) {
                                document.title = newTitle.textContent;
                            }
                            
                            // Update URL if needed
                            if (pushState) {
                                history.pushState({page: url}, '', url);
                            }
                            
                            // Fade in new content
                            mainContent.style.opacity = '1';
                            mainContent.style.transform = 'translateY(0)';
                            
                            // Reinitialize any JavaScript components
                            reinitializeComponents();
                            
                            hideLoadingState();
                            isLoading = false;
                        }, 300);
                    } else {
                        throw new Error('Could not find main content in response');
                    }
                })
                .catch(error => {
                    console.error('Error loading page:', error);
                    hideLoadingState();
                    isLoading = false;
                    
                    // Fallback to normal navigation
                    if (clickedLink) {
                        window.location.href = url;
                    }
                });
        }
        
        // Update active navigation state
        function updateActiveNavigation(clickedLink) {
            // Remove active class from all nav links
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
            });
            
            // Add active class to clicked link
            clickedLink.classList.add('active');
        }
        
        // Show loading state
        function showLoadingState() {
            // Create and show progress bar
            let progressBar = document.getElementById('loading-progress');
            if (!progressBar) {
                progressBar = document.createElement('div');
                progressBar.id = 'loading-progress';
                progressBar.className = 'loading-progress';
                document.body.appendChild(progressBar);
            }
            
            progressBar.classList.add('active');
            progressBar.style.width = '30%';
            
            // Simulate progress
            setTimeout(() => {
                progressBar.style.width = '60%';
            }, 150);
            
            setTimeout(() => {
                progressBar.style.width = '90%';
            }, 300);
        }
        
        // Hide loading state
        function hideLoadingState() {
            const progressBar = document.getElementById('loading-progress');
            if (progressBar) {
                progressBar.style.width = '100%';
                setTimeout(() => {
                    progressBar.classList.remove('active');
                    progressBar.style.width = '0%';
                }, 200);
            }
        }
        
        // Reinitialize components after AJAX load
        function reinitializeComponents() {
            // Destroy existing DataTables first
            if (typeof $.fn.DataTable !== 'undefined') {
                $('.datatable').each(function() {
                    if ($.fn.DataTable.isDataTable(this)) {
                        $(this).DataTable().destroy();
                    }
                });
                
                // Reinitialize DataTables
                $('.datatable').DataTable({
                    "pageLength": 10,
                    "order": [[0, "desc"]]
                });
            }
            
            // Reinitialize AJAX navigation for new content
            initializeAjaxNavigation();
            
            // Reinitialize Bootstrap components
            if (typeof bootstrap !== 'undefined') {
                // Reinitialize tooltips
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
                
                // Reinitialize dropdowns
                const dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
                dropdownElementList.map(function (dropdownToggleEl) {
                    return new bootstrap.Dropdown(dropdownToggleEl);
                });
            }
            
            // Auto-hide alerts
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
            
            // Scroll to top smoothly
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }
        
        // Initialize AJAX navigation when DOM is ready
        $(document).ready(function() {
            initializeAjaxNavigation();
            
            // Set initial state for browser history
            history.replaceState({page: window.location.pathname}, '', window.location.pathname);
        });
    </script>
</body>
</html>
