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
                "order": [[0, "desc"]],
                "initComplete": function() {
                    // Force apply custom styling after initialization
                    applyDataTablesStyling();
                }
            });
        });
        
        // Function to apply custom DataTables styling
        function applyDataTablesStyling() {
            // Force transparent backgrounds on all DataTables elements
            $('.dataTables_wrapper, .dataTables_wrapper *').css({
                'background-color': 'transparent !important',
                'background': 'transparent !important'
            });
            
            // Force table backgrounds to be transparent
            $('.dataTables_wrapper table, .dataTables_wrapper table.dataTable, .dataTables_wrapper table.dataTable thead, .dataTables_wrapper table.dataTable thead th, .dataTables_wrapper table.dataTable tbody, .dataTables_wrapper table.dataTable tbody tr, .dataTables_wrapper table.dataTable tbody td').css({
                'background-color': 'transparent !important',
                'background': 'transparent !important',
                'color': 'var(--text-color) !important'
            });
            
            // Force styling on pagination buttons
            $('.dataTables_wrapper .dataTables_paginate .paginate_button').each(function() {
                $(this).css({
                    'background': 'var(--card-bg)',
                    'border': '1px solid var(--border-color)',
                    'color': 'var(--text-color)',
                    'border-radius': '6px',
                    'padding': '6px 12px',
                    'margin': '0 2px',
                    'transition': 'all 0.3s ease'
                });
            });
            
            // Force styling on current page button
            $('.dataTables_wrapper .dataTables_paginate .paginate_button.current').css({
                'background': 'var(--coffee-primary)',
                'color': 'white',
                'border-color': 'var(--coffee-primary)',
                'box-shadow': '0 2px 4px rgba(139, 69, 19, 0.3)'
            });
            
            // Force styling on disabled buttons
            $('.dataTables_wrapper .dataTables_paginate .paginate_button.disabled').css({
                'background': 'var(--card-bg)',
                'color': 'var(--text-muted)',
                'border-color': 'var(--border-color)',
                'opacity': '0.5',
                'cursor': 'not-allowed'
            });
            
            // Force styling on controls
            $('.dataTables_wrapper .dataTables_length select, .dataTables_wrapper .dataTables_filter input').css({
                'background': 'var(--card-bg)',
                'color': 'var(--text-color)',
                'border': '1px solid var(--border-color)',
                'border-radius': '6px',
                'padding': '6px 12px'
            });
            
            // Force styling on labels
            $('.dataTables_wrapper .dataTables_length label, .dataTables_wrapper .dataTables_filter label').css({
                'color': 'var(--text-color)',
                'font-weight': '500'
            });
            
            // Force styling on info text
            $('.dataTables_wrapper .dataTables_info').css({
                'color': 'var(--text-muted)',
                'font-size': '14px'
            });
            
            // Force wrapper backgrounds to be transparent
            $('.dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_filter, .dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_paginate, .dataTables_wrapper .dataTables_processing').css({
                'background-color': 'transparent !important',
                'background': 'transparent !important'
            });
        }
        
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
            
            // Refresh DataTables styling after theme change
            setTimeout(() => {
                refreshDataTablesStyling();
            }, 100);
        }
        
        // Function to refresh DataTables styling
        function refreshDataTablesStyling() {
            if (typeof $.fn.DataTable !== 'undefined') {
                $('.datatable').each(function() {
                    if ($.fn.DataTable.isDataTable(this)) {
                        const table = $(this).DataTable();
                        // Force redraw to apply new styling
                        table.draw(false);
                    }
                });
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
                    const href = this.getAttribute('href');
                    
                    // Don't load if it's the current page
                    if (href === window.location.pathname.split('/').pop()) {
                        return;
                    }
                    
                    // Skip AJAX for reports.php and load directly
                    if (href === 'reports.php') {
                        window.location.href = 'reports.php';
                        return;
                    }
                    
                    e.preventDefault();
                    
                    // Add loading state to clicked link
                    this.style.opacity = '0.7';
                    this.style.pointerEvents = 'none';
                    
                    loadPage(href, this);
                    
                    // Reset link state after a delay
                    setTimeout(() => {
                        this.style.opacity = '1';
                        this.style.pointerEvents = 'auto';
                    }, 1000);
                });
            });
            
            // Handle browser back/forward buttons
            window.addEventListener('popstate', function(e) {
                if (e.state && e.state.page) {
                    loadPage(e.state.page, null, false);
                }
            });
        }
        
        // Load page content via AJAX with automatic refresh
        function loadPage(url, clickedLink = null, pushState = true) {
            if (isLoading) return;
            
            isLoading = true;
            showLoadingState();
            
            // Update active navigation
            if (clickedLink) {
                updateActiveNavigation(clickedLink);
            }
            
            // Add cache-busting parameter to force refresh
            const separator = url.includes('?') ? '&' : '?';
            const refreshUrl = url + separator + '_refresh=' + Date.now();
            
            fetch(refreshUrl, {
                method: 'GET',
                headers: {
                    'Cache-Control': 'no-cache, no-store, must-revalidate',
                    'Pragma': 'no-cache',
                    'Expires': '0'
                }
            })
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
                            
                            // Trigger automatic refresh for dynamic content
                            triggerAutoRefresh();
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
                    "order": [[0, "desc"]],
                    "initComplete": function() {
                        // Force apply custom styling after initialization
                        applyDataTablesStyling();
                    }
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
            
            // Reinitialize page-specific JavaScript functionality
            reinitializePageJavaScript();
            
            // Restart periodic refresh for new page
            startPeriodicRefresh();
            
            // Debug: Log successful reinitialization
            console.log('Components reinitialized for:', window.location.pathname.split('/').pop());
        }
        
        // Reinitialize page-specific JavaScript functionality
        function reinitializePageJavaScript() {
            const currentPage = window.location.pathname.split('/').pop();
            
            switch(currentPage) {
                case 'sales.php':
                    reinitializeSalesPage();
                    break;
                case 'reports.php':
                    // Reports page loads directly, no reinitialization needed
                    break;
                case 'users.php':
                    reinitializeUsersPage();
                    break;
                case 'products.php':
                    reinitializeProductsPage();
                    break;
                case 'my_activity.php':
                    reinitializeActivityPage();
                    break;
                case 'dashboard.php':
                    reinitializeDashboardPage();
                    break;
            }
        }
        
        // Sales page JavaScript reinitialization
        function reinitializeSalesPage() {
            // Reset cart array
            window.cart = [];
            
            // Reinitialize search functionality
            const searchInput = document.getElementById('searchProduct');
            if (searchInput) {
                // Remove existing event listeners by cloning the element
                const newSearchInput = searchInput.cloneNode(true);
                searchInput.parentNode.replaceChild(newSearchInput, searchInput);
                
                // Add new event listener
                newSearchInput.addEventListener('input', function(e) {
                    const searchTerm = e.target.value.toLowerCase();
                    const products = document.querySelectorAll('.product-item');
                    
                    products.forEach(product => {
                        const name = product.getAttribute('data-name');
                        const category = product.getAttribute('data-category');
                        
                        if (name.includes(searchTerm) || category.includes(searchTerm)) {
                            product.style.display = 'block';
                        } else {
                            product.style.display = 'none';
                        }
                    });
                    
                    // If searching, show all products tab and hide others
                    if (searchTerm.length > 0) {
                        // Show all products tab
                        const allTab = document.getElementById('all-tab');
                        if (allTab) {
                            allTab.click();
                        }
                    }
                });
            }
            
            // Reinitialize product click handlers
            const productCards = document.querySelectorAll('.product-card');
            productCards.forEach(card => {
                // Store the original onclick attribute value
                const originalOnclick = card.getAttribute('onclick');
                if (originalOnclick) {
                    // Remove the onclick attribute to prevent duplicate handlers
                    card.removeAttribute('onclick');
                    
                    // Add new event listener that executes the original onclick code
                    card.addEventListener('click', function() {
                        // Extract the product data from the original onclick
                        const match = originalOnclick.match(/addToCart\((.+)\)/);
                        if (match) {
                            try {
                                const productData = JSON.parse(match[1]);
                                window.addToCart(productData);
                            } catch (e) {
                                console.error('Error parsing product data:', e);
                            }
                        }
                    });
                }
            });
            
            // Reinitialize cart functions globally
            window.addToCart = function(product) {
                const existingItem = window.cart.find(item => item.product_id === product.id);
                
                if (existingItem) {
                    if (existingItem.quantity < product.stock) {
                        existingItem.quantity++;
                    } else {
                        alert('Cannot add more. Stock limit reached!');
                        return;
                    }
                } else {
                    window.cart.push({
                        product_id: product.id,
                        name: product.name,
                        price: parseFloat(product.price),
                        quantity: 1,
                        max_stock: product.stock
                    });
                }
                
                updateCart();
            };
            
            window.removeFromCart = function(productId) {
                window.cart = window.cart.filter(item => item.product_id !== productId);
                updateCart();
            };
            
            window.updateQuantity = function(productId, quantity) {
                const item = window.cart.find(item => item.product_id === productId);
                if (item) {
                    quantity = parseInt(quantity);
                    if (quantity > 0 && quantity <= item.max_stock) {
                        item.quantity = quantity;
                        updateCart();
                    } else if (quantity > item.max_stock) {
                        alert('Quantity exceeds available stock!');
                        document.getElementById(`qty_${productId}`).value = item.quantity;
                    }
                }
            };
            
            window.clearCart = function() {
                if (confirm('Are you sure you want to clear the cart?')) {
                    window.cart = [];
                    updateCart();
                }
            };
            
            window.processSale = function() {
                if (window.cart.length === 0) {
                    alert('Cart is empty!');
                    return;
                }
                const confirmModal = new bootstrap.Modal(document.getElementById('confirmSaleModal'));
                confirmModal.show();
                document.getElementById('confirmProcessBtn').onclick = () => {
                    confirmModal.hide();
                    new bootstrap.Modal(document.getElementById('processingModal')).show();
                    document.getElementById('saleItems').value = JSON.stringify(window.cart);
                    document.getElementById('saleForm').submit();
                };
            };
            
            // Function to load receipt in modal
            window.loadReceiptModal = function(saleId) {
                // Show loading state
                document.getElementById('receiptContent').innerHTML = `
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary mb-3" role="status"></div>
                        <div class="fw-semibold">Loading receipt...</div>
                    </div>
                `;
                
                // Show receipt modal
                const receiptModal = new bootstrap.Modal(document.getElementById('receiptModal'));
                receiptModal.show();
                
                // Load receipt content
                fetch(`receipt.php?id=${saleId}&modal=true`)
                    .then(response => response.text())
                    .then(html => {
                        document.getElementById('receiptContent').innerHTML = html;
                        
                        // Set up print button
                        document.getElementById('printReceiptBtn').onclick = () => {
                            window.open(`receipt.php?id=${saleId}`, '_blank');
                        };
                    })
                    .catch(error => {
                        console.error('Error loading receipt:', error);
                        document.getElementById('receiptContent').innerHTML = `
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Error loading receipt. Please try again.
                            </div>
                        `;
                    });
            };
            
            window.updateCart = function() {
                const cartDiv = document.getElementById('cartItems');
                const clearBtn = document.getElementById('clearBtn');
                const checkoutBtn = document.getElementById('checkoutBtn');
                
                if (window.cart.length === 0) {
                    cartDiv.innerHTML = '<p class="text-center text-muted py-4">Cart is empty</p>';
                    clearBtn.disabled = true;
                    checkoutBtn.disabled = true;
                } else {
                    let html = '';
                    let total = 0;
                    
                    window.cart.forEach(item => {
                        const subtotal = item.price * item.quantity;
                        total += subtotal;
                        
                        html += `
                            <div class="cart-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0">${item.name}</h6>
                                        <small class="text-muted">₱${item.price.toFixed(2)} each</small>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <input type="number" 
                                               class="form-control form-control-sm me-2" 
                                               style="width: 60px;"
                                               id="qty_${item.product_id}"
                                               value="${item.quantity}"
                                               min="1"
                                               max="${item.max_stock}"
                                               onchange="updateQuantity(${item.product_id}, this.value)">
                                        <span class="me-2">₱${subtotal.toFixed(2)}</span>
                                        <button class="btn btn-sm btn-danger" 
                                                onclick="removeFromCart(${item.product_id})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    
                    cartDiv.innerHTML = html;
                    document.getElementById('subtotal').textContent = '₱' + total.toFixed(2);
                    document.getElementById('total').textContent = '₱' + total.toFixed(2);
                    
                    clearBtn.disabled = false;
                    checkoutBtn.disabled = false;
                }
            };
            
            // Reinitialize print receipt function
            window.loadReceiptModal = function(saleId) {
                fetch(`receipt.php?id=${saleId}&modal=true`)
                    .then(response => response.text())
                    .then(html => {
                        document.getElementById('receiptContent').innerHTML = html;
                        
                        // Set up print button
                        const printBtn = document.getElementById('printReceiptBtn');
                        if (printBtn) {
                            printBtn.onclick = () => {
                                window.open(`receipt.php?id=${saleId}`, '_blank');
                            };
                        }
                    })
                    .catch(error => {
                        console.error('Error loading receipt:', error);
                        alert('Error loading receipt. Please try again.');
                    });
            };
            
            console.log('Sales page JavaScript reinitialized');
        }
        
        // Reports page JavaScript reinitialization
        function reinitializeReportsPage() {
            // Reinitialize report functions
            window.viewSaleDetails = function(saleId) {
                fetch(`ajax/get_sale_details.php?id=${saleId}`)
                    .then(response => response.text())
                    .then(html => {
                        document.getElementById('saleDetailsContent').innerHTML = html;
                        new bootstrap.Modal(document.getElementById('saleDetailsModal')).show();
                    });
            };
            
            window.exportToCSV = function() {
                const table = document.getElementById('salesTable');
                let csv = 'Sale ID,Date,Cashier,Amount\n';
                
                const rows = table.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    if (cells.length >= 4) {
                        // Clean and format each cell
                        const saleId = cells[0].textContent.trim().replace('#', '');
                        const dateTime = cells[1].textContent.trim();
                        const cashier = cells[2].textContent.trim();
                        const amount = cells[3].textContent.trim().replace('₱', '').replace(',', '');
                        
                        // Format date to "Month Date, Year" format
                        let formattedDate = dateTime;
                        try {
                            // Try to parse the date and reformat it
                            const date = new Date(dateTime);
                            if (!isNaN(date.getTime())) {
                                formattedDate = date.toLocaleDateString('en-US', {
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric'
                                });
                            }
                        } catch (e) {
                            // If parsing fails, use original date
                            formattedDate = dateTime;
                        }
                        
                        // Ensure date is treated as text by Excel
                        formattedDate = `'${formattedDate}`;
                        
                        // Format amount with peso sign
                        const formattedAmount = `₱${amount}`;
                        
                        // Escape commas and quotes in data
                        const cleanSaleId = `"${saleId}"`;
                        const cleanDate = `"${formattedDate}"`;
                        const cleanCashier = `"${cashier}"`;
                        const cleanAmount = `"${formattedAmount}"`;
                        
                        csv += `${cleanSaleId},${cleanDate},${cleanCashier},${cleanAmount}\n`;
                    }
                });
                
                downloadCSV(csv, 'sales_report.csv');
            };
            
            window.exportProductsToCSV = function() {
                const table = document.getElementById('productsTable');
                let csv = 'Product,Category,Units Sold,Times Sold,Revenue\n';
                
                const rows = table.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    if (cells.length >= 5) {
                        // Clean and format each cell
                        const product = cells[0].textContent.trim();
                        const category = cells[1].textContent.trim();
                        const unitsSold = cells[2].textContent.trim().replace(',', '');
                        const timesSold = cells[3].textContent.trim().replace(',', '');
                        const revenue = cells[4].textContent.trim().replace('₱', '').replace(',', '');
                        
                        // Format revenue with peso sign
                        const formattedRevenue = `₱${revenue}`;
                        
                        // Escape commas and quotes in data
                        const cleanProduct = `"${product}"`;
                        const cleanCategory = `"${category}"`;
                        const cleanUnitsSold = `"${unitsSold}"`;
                        const cleanTimesSold = `"${timesSold}"`;
                        const cleanRevenue = `"${formattedRevenue}"`;
                        
                        csv += `${cleanProduct},${cleanCategory},${cleanUnitsSold},${cleanTimesSold},${cleanRevenue}\n`;
                    }
                });
                
                downloadCSV(csv, 'products_report.csv');
            };
            
            window.downloadCSV = function(csv, filename) {
                const blob = new Blob([csv], { type: 'text/csv' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = filename;
                a.click();
                window.URL.revokeObjectURL(url);
            };
            
            window.exportToPDF = function() {
                // Get the table element
                const table = document.getElementById('salesTable');
                if (!table) {
                    alert('Sales table not found');
                    return;
                }
                
                // Clone the table and remove the Action column
                const clonedTable = table.cloneNode(true);
                const rows = clonedTable.querySelectorAll('tr');
                
                rows.forEach(row => {
                    const cells = row.querySelectorAll('td, th');
                    // Remove the last column (Action column)
                    if (cells.length > 4) {
                        cells[cells.length - 1].remove();
                    }
                });
                
                // Create a new window with only the table (without Action column)
                const printWindow = window.open('', '_blank');
                printWindow.document.write(`
                    <html>
                    <head>
                        <title>Sales Report - Camp Of Coffee</title>
                        <style>
                            body {
                                font-family: Arial, sans-serif;
                                margin: 20px;
                                color: #333;
                            }
                            .header {
                                text-align: center;
                                margin-bottom: 30px;
                                border-bottom: 2px solid #333;
                                padding-bottom: 20px;
                            }
                            .logo-container {
                                margin-bottom: 15px;
                            }
                            .company-logo {
                                width: 80px;
                                height: 80px;
                                border-radius: 50%;
                                object-fit: cover;
                                border: 2px solid #333;
                            }
                            .company-name {
                                font-size: 24px;
                                font-weight: bold;
                                margin-bottom: 10px;
                            }
                            .report-title {
                                font-size: 18px;
                                color: #666;
                                margin-bottom: 5px;
                            }
                            .report-date {
                                font-size: 14px;
                                color: #888;
                            }
                            table {
                                width: 100%;
                                border-collapse: collapse;
                                margin-top: 20px;
                            }
                            th, td {
                                border: 1px solid #ddd;
                                padding: 12px;
                                text-align: left;
                            }
                            th {
                                background-color: #f5f5f5;
                                font-weight: bold;
                                color: #333;
                            }
                            tr:nth-child(even) {
                                background-color: #f9f9f9;
                            }
                            tr:hover {
                                background-color: #f5f5f5;
                            }
                            .amount {
                                text-align: right;
                                font-weight: bold;
                            }
                            .footer {
                                margin-top: 30px;
                                text-align: center;
                                font-size: 12px;
                                color: #666;
                                border-top: 1px solid #ddd;
                                padding-top: 20px;
                            }
                            @media print {
                                body { margin: 0; }
                                .header { margin-bottom: 20px; }
                            }
                        </style>
                    </head>
                    <body>
                        <div class="header">
                            <div class="logo-container">
                                <img src="assets/images/coc_logo.jpg" alt="Camp Of Coffee Logo" class="company-logo">
                            </div>
                            <div class="company-name">Camp Of Coffee</div>
                            <div class="report-title">Sales Report</div>
                            <div class="report-date">Generated on: ${new Date().toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            })}</div>
                        </div>
                        
                        ${clonedTable.outerHTML}
                        
                        <div class="footer">
                            <p>Thank you for using Camp Of Coffee Sales System</p>
                            <p>This report was generated automatically</p>
                        </div>
                    </body>
                    </html>
                `);
                printWindow.document.close();
                printWindow.focus();
                setTimeout(() => {
                    printWindow.print();
                }, 500);
            };
            
            console.log('Reports page JavaScript reinitialized');
        }
        
        // Global export functions for all pages
        window.downloadCSV = function(csv, filename) {
            // Add BOM for proper UTF-8 encoding in Excel
            const BOM = '\uFEFF';
            const csvWithBOM = BOM + csv;
            
            const blob = new Blob([csvWithBOM], { type: 'text/csv;charset=utf-8;' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            a.style.display = 'none';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        };
        
        window.exportReceiptToPDF = function(saleId) {
            // Create a new window with the receipt content for printing/PDF
            const receiptContent = document.querySelector('.receipt-container');
            if (!receiptContent) {
                alert('Receipt content not found');
                return;
            }
            
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                <head>
                    <title>Receipt #${saleId}</title>
                    <style>
                        body { margin: 0; padding: 20px; font-family: Arial, sans-serif; }
                        .receipt-container {
                            background: white;
                            border-radius: 8px;
                            padding: 20px;
                            max-width: 400px;
                            margin: 0 auto;
                            font-family: Arial, sans-serif;
                            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                        }
                        .receipt-header { text-align: center; margin-bottom: 20px; }
                        .receipt-logo { width: 60px; height: 60px; border-radius: 50%; margin: 0 auto 10px; display: block; object-fit: cover; }
                        .receipt-title { font-size: 18px; font-weight: bold; color: #333; margin-bottom: 5px; }
                        .receipt-details { font-size: 12px; color: #666; margin-bottom: 15px; }
                        .receipt-section { margin-bottom: 15px; }
                        .receipt-items-header { font-size: 16px; font-weight: bold; color: #333; text-align: center; margin-bottom: 10px; border-top: 1px dashed #ccc; border-bottom: 1px dashed #ccc; padding: 8px 0; }
                        .receipt-table { width: 100%; border-collapse: collapse; font-size: 13px; }
                        .receipt-table th { font-weight: bold; color: #333; padding: 6px 4px; text-align: left; }
                        .receipt-table th:nth-child(2), .receipt-table th:nth-child(3), .receipt-table th:nth-child(4) { text-align: center; }
                        .receipt-table th:nth-child(4) { text-align: right; }
                        .receipt-table td { padding: 6px 4px; color: #333; }
                        .receipt-table td:nth-child(2), .receipt-table td:nth-child(3) { text-align: center; }
                        .receipt-table td:nth-child(4) { text-align: right; }
                        .receipt-total { border-top: 1px dashed #ccc; padding-top: 10px; margin-top: 10px; }
                        .receipt-total-amount { font-size: 16px; font-weight: bold; color: #333; text-align: right; }
                        .receipt-footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
                        @media print { body { margin: 0; } }
                    </style>
                </head>
                <body>
                    ${receiptContent.outerHTML}
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.focus();
            setTimeout(() => {
                printWindow.print();
            }, 500);
        };
        
        window.exportReceiptToJPG = function(saleId) {
            // Use html2canvas to convert receipt to image
            if (typeof html2canvas === 'undefined') {
                // Load html2canvas library if not already loaded
                const script = document.createElement('script');
                script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js';
                script.onload = () => captureReceiptAsJPG(saleId);
                document.head.appendChild(script);
            } else {
                captureReceiptAsJPG(saleId);
            }
        };
        
        function captureReceiptAsJPG(saleId) {
            const receiptElement = document.querySelector('.receipt-container');
            if (!receiptElement) {
                alert('Receipt content not found');
                return;
            }
            
            html2canvas(receiptElement, {
                backgroundColor: '#ffffff',
                scale: 2,
                useCORS: true,
                allowTaint: true
            }).then(canvas => {
                // Convert canvas to blob and download
                canvas.toBlob(function(blob) {
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `receipt_${saleId}.jpg`;
                    a.style.display = 'none';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    URL.revokeObjectURL(url);
                }, 'image/jpeg', 0.95);
            }).catch(error => {
                console.error('Error capturing receipt:', error);
                alert('Error capturing receipt. Please try again.');
            });
        }
        
        // Users page JavaScript reinitialization
        function reinitializeUsersPage() {
            // Reinitialize user management functions
            window.editUser = function(user) {
                document.getElementById('edit_id').value = user.id;
                document.getElementById('edit_username').value = user.username;
                document.getElementById('edit_role').value = user.role;
                document.getElementById('edit_full_name').value = user.full_name || '';
                document.getElementById('edit_email').value = user.email || '';
                document.getElementById('edit_phone').value = user.phone || '';
                document.getElementById('edit_password').value = '';
                
                new bootstrap.Modal(document.getElementById('editUserModal')).show();
            };
            
            window.deleteUser = function(id, username) {
                document.getElementById('delete_id').value = id;
                document.getElementById('delete_username').textContent = username;
                
                new bootstrap.Modal(document.getElementById('deleteUserModal')).show();
            };
            
            console.log('Users page JavaScript reinitialized');
        }
        
        // Products page JavaScript reinitialization
        function reinitializeProductsPage() {
            // Reinitialize product management functions
            window.editProduct = function(product) {
                document.getElementById('edit_id').value = product.id;
                document.getElementById('edit_name').value = product.name;
                document.getElementById('edit_category').value = product.category;
                document.getElementById('edit_price').value = product.price;
                document.getElementById('edit_stock').value = product.stock;
                
                new bootstrap.Modal(document.getElementById('editProductModal')).show();
            };
            
            window.deleteProduct = function(id, name) {
                document.getElementById('delete_id').value = id;
                document.getElementById('delete_name').textContent = name;
                
                new bootstrap.Modal(document.getElementById('deleteProductModal')).show();
            };
            
            console.log('Products page JavaScript reinitialized');
        }
        
        // Activity page JavaScript reinitialization
        function reinitializeActivityPage() {
            // Add any activity page specific JavaScript here
            console.log('Activity page JavaScript reinitialized');
        }
        
        // Dashboard page JavaScript reinitialization
        function reinitializeDashboardPage() {
            // Add any dashboard page specific JavaScript here
            console.log('Dashboard page JavaScript reinitialized');
        }
        
        // Auto-refresh functionality for dynamic content
        function triggerAutoRefresh() {
            // Refresh dynamic content like sales data, products, etc.
            const currentPage = window.location.pathname.split('/').pop();
            
            // Auto-refresh specific page content based on current page
            switch(currentPage) {
                case 'sales.php':
                    // Refresh sales data if there's a sales table or recent sales
                    refreshSalesData();
                    break;
                case 'dashboard.php':
                    // Refresh dashboard stats
                    refreshDashboardStats();
                    break;
                case 'products.php':
                    // Refresh product data
                    refreshProductsData();
                    break;
                case 'reports.php':
                    // Refresh reports data
                    refreshReportsData();
                    break;
                case 'my_activity.php':
                    // Refresh activity data
                    refreshActivityData();
                    break;
                case 'users.php':
                    // Refresh users data
                    refreshUsersData();
                    break;
            }
        }
        
        // Refresh functions for different pages
        function refreshSalesData() {
            // Refresh today's sales section if it exists
            const todaySalesSection = document.querySelector('.card .table-responsive');
            if (todaySalesSection && window.location.pathname.includes('sales.php')) {
                // Add a subtle refresh indicator
                const refreshIndicator = document.createElement('div');
                refreshIndicator.className = 'text-center text-muted small mb-2';
                refreshIndicator.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Data refreshed';
                refreshIndicator.style.opacity = '0';
                refreshIndicator.style.transition = 'opacity 0.3s ease';
                
                // Insert refresh indicator
                const cardBody = todaySalesSection.closest('.card-body');
                if (cardBody) {
                    cardBody.insertBefore(refreshIndicator, cardBody.firstChild);
                    
                    // Animate in
                    setTimeout(() => {
                        refreshIndicator.style.opacity = '1';
                    }, 100);
                    
                    // Remove after 2 seconds
                    setTimeout(() => {
                        refreshIndicator.style.opacity = '0';
                        setTimeout(() => {
                            if (refreshIndicator.parentNode) {
                                refreshIndicator.parentNode.removeChild(refreshIndicator);
                            }
                        }, 300);
                    }, 2000);
                }
            }
        }
        
        function refreshDashboardStats() {
            // Add refresh indicator to dashboard
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach(card => {
                card.style.transform = 'scale(1.02)';
                card.style.transition = 'transform 0.3s ease';
                setTimeout(() => {
                    card.style.transform = 'scale(1)';
                }, 300);
            });
        }
        
        function refreshProductsData() {
            // Refresh products grid
            const productGrid = document.getElementById('productGrid');
            if (productGrid) {
                productGrid.style.opacity = '0.8';
                setTimeout(() => {
                    productGrid.style.opacity = '1';
                }, 300);
            }
        }
        
        function refreshReportsData() {
            // Refresh reports content
            const reportContent = document.querySelector('.card-body');
            if (reportContent) {
                reportContent.style.transform = 'translateY(-5px)';
                reportContent.style.transition = 'transform 0.3s ease';
                setTimeout(() => {
                    reportContent.style.transform = 'translateY(0)';
                }, 300);
            }
        }
        
        function refreshActivityData() {
            // Refresh activity table
            const activityTable = document.querySelector('.table');
            if (activityTable) {
                activityTable.style.transform = 'translateX(-5px)';
                activityTable.style.transition = 'transform 0.3s ease';
                setTimeout(() => {
                    activityTable.style.transform = 'translateX(0)';
                }, 300);
            }
        }
        
        function refreshUsersData() {
            // Refresh users table
            const usersTable = document.querySelector('.table');
            if (usersTable) {
                usersTable.style.transform = 'translateX(5px)';
                usersTable.style.transition = 'transform 0.3s ease';
                setTimeout(() => {
                    usersTable.style.transform = 'translateX(0)';
                }, 300);
            }
        }
        
        // Add page refresh indicator
        function showPageRefreshIndicator() {
            // Create refresh indicator
            const indicator = document.createElement('div');
            indicator.id = 'page-refresh-indicator';
            indicator.style.cssText = `
                position: fixed;
                top: 20px;
                left: 50%;
                transform: translateX(-50%);
                background: var(--coffee-primary);
                color: white;
                padding: 8px 16px;
                border-radius: 20px;
                font-size: 14px;
                z-index: 10000;
                box-shadow: 0 2px 10px rgba(0,0,0,0.2);
                opacity: 0;
                transition: opacity 0.3s ease;
            `;
            indicator.innerHTML = '<i class="bi bi-arrow-clockwise me-2"></i>Page refreshed';
            
            document.body.appendChild(indicator);
            
            // Animate in
            setTimeout(() => {
                indicator.style.opacity = '1';
            }, 100);
            
            // Remove after 2 seconds
            setTimeout(() => {
                indicator.style.opacity = '0';
                setTimeout(() => {
                    if (indicator.parentNode) {
                        indicator.parentNode.removeChild(indicator);
                    }
                }, 300);
            }, 2000);
        }
        
        // Periodic refresh for dynamic pages
        let refreshInterval = null;
        
        function startPeriodicRefresh() {
            // Clear any existing interval
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
            
            const currentPage = window.location.pathname.split('/').pop();
            
            // Set different refresh intervals for different pages
            let intervalTime = 0;
            switch(currentPage) {
                case 'sales.php':
                    intervalTime = 30000; // 30 seconds for sales page
                    break;
                case 'dashboard.php':
                    intervalTime = 60000; // 1 minute for dashboard
                    break;
                case 'reports.php':
                    intervalTime = 120000; // 2 minutes for reports
                    break;
                case 'my_activity.php':
                    intervalTime = 45000; // 45 seconds for activity
                    break;
                default:
                    intervalTime = 0; // No auto-refresh for other pages
                    break;
            }
            
            // Start periodic refresh if interval is set
            if (intervalTime > 0) {
                refreshInterval = setInterval(() => {
                    // Only refresh if user is not actively interacting
                    if (!document.hidden && !isLoading) {
                        console.log('Auto-refreshing page content...');
                        triggerAutoRefresh();
                        showPageRefreshIndicator();
                    }
                }, intervalTime);
            }
        }
        
        // Stop periodic refresh
        function stopPeriodicRefresh() {
            if (refreshInterval) {
                clearInterval(refreshInterval);
                refreshInterval = null;
            }
        }
        
        // Handle page visibility changes
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                stopPeriodicRefresh();
            } else {
                startPeriodicRefresh();
            }
        });
        
        // Initialize AJAX navigation when DOM is ready
        $(document).ready(function() {
            initializeAjaxNavigation();
            
            // Set initial state for browser history
            history.replaceState({page: window.location.pathname}, '', window.location.pathname);
            
            // Show initial refresh indicator
            setTimeout(() => {
                showPageRefreshIndicator();
            }, 500);
            
            // Start periodic refresh for current page
            startPeriodicRefresh();
            
            // Initialize page-specific JavaScript on initial load
            reinitializePageJavaScript();
            
            // Debug: Log current page
            console.log('Page loaded:', window.location.pathname.split('/').pop());
        });
    </script>
</body>
</html>
