<?php
// Include required files first (but not header yet)
require_once 'config/session.php';
requireLogin();
require_once 'includes/products.php';
require_once 'includes/sales.php';

// Handle sale submission BEFORE any output
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['items'])) {
    $items = json_decode($_POST['items'], true);
    
    if (!empty($items)) {
        $result = createSale($items, getCurrentUserId());
        
        if ($result['success']) {
            $_SESSION['last_sale_id'] = $result['sale_id'];
            $_SESSION['success'] = 'Sale completed successfully! Sale ID: #' . $result['sale_id'];
            error_log("Sale created successfully: ID = " . $result['sale_id']);
            header('Location: sales.php');
            exit();
        } else {
            $error = 'Failed to process sale: ' . $result['message'];
            error_log("Sale creation failed: " . $result['message']);
        }
    } else {
        error_log("Sale creation failed: No items provided");
    }
}

// Get all products for POS
$products = getAllProducts();
$categories = getProductCategories();

// NOW include header after all processing is done
$pageTitle = 'Sales';
require_once 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col">
        <h2 class="mb-0">
            <i class="bi bi-cart-check me-2"></i>Point of Sale
        </h2>
    </div>
</div>

<?php if (isset($_SESSION['success'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i>
    <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if (isset($error)): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    <?php echo htmlspecialchars($error); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row">
    <!-- Products Section -->
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-box-seam me-2"></i>Products
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <input type="text" class="form-control" id="searchProduct" 
                           placeholder="Search products...">
                </div>
                
                <!-- Category Tabs -->
                <ul class="nav nav-tabs mb-3" id="categoryTabs" role="tablist">
                    <li class="nav-item me-2" role="presentation">
                        <button class="nav-link active" id="all-tab" data-bs-toggle="tab" 
                                data-bs-target="#all" type="button" role="tab" aria-controls="all" 
                                aria-selected="true">
                            All Products 
                            <span class="badge bg-secondary ms-1"><?php echo count(array_filter($products, function($p) { return $p['stock'] > 0; })); ?></span>
                        </button>
                    </li>
                    <?php foreach ($categories as $category): ?>
                    <?php 
                    $categoryCount = count(array_filter($products, function($product) use ($category) {
                        return $product['category'] === $category && $product['stock'] > 0;
                    }));
                    ?>
                    <li class="nav-item me-2" role="presentation">
                        <button class="nav-link" id="<?php echo strtolower(str_replace(' ', '-', $category)); ?>-tab" 
                                data-bs-toggle="tab" data-bs-target="#<?php echo strtolower(str_replace(' ', '-', $category)); ?>" 
                                type="button" role="tab" aria-controls="<?php echo strtolower(str_replace(' ', '-', $category)); ?>" 
                                aria-selected="false">
                            <?php echo htmlspecialchars($category); ?>
                            <span class="badge bg-secondary ms-1"><?php echo $categoryCount; ?></span>
                        </button>
                    </li>
                    <?php endforeach; ?>
                </ul>
                
                <!-- Tab Content -->
                <div class="tab-content" id="categoryTabContent">
                    <!-- All Products Tab -->
                    <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                        <div class="row" id="productGrid">
                            <?php foreach ($products as $product): ?>
                            <?php if ($product['stock'] > 0): ?>
                            <div class="col-md-4 col-sm-6 mb-3 product-item" 
                                 data-name="<?php echo strtolower($product['name']); ?>"
                                 data-category="<?php echo strtolower($product['category']); ?>">
                                <div class="card h-100 product-card" 
                                     onclick="addToCart(<?php echo htmlspecialchars(json_encode($product)); ?>)"
                                     style="cursor: pointer; transition: all 0.3s;">
                                    <div class="card-body text-center">
                                        <h6 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h6>
                                        <p class="text-muted mb-1">
                                            <small><?php echo htmlspecialchars($product['category']); ?></small>
                                        </p>
                                        <p class="fw-bold mb-1">₱<?php echo number_format($product['price'], 2); ?></p>
                                        <p class="text-muted mb-0">
                                            <small>Stock: <?php echo $product['stock']; ?></small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Category-specific tabs -->
                    <?php foreach ($categories as $category): ?>
                    <div class="tab-pane fade" id="<?php echo strtolower(str_replace(' ', '-', $category)); ?>" 
                         role="tabpanel" aria-labelledby="<?php echo strtolower(str_replace(' ', '-', $category)); ?>-tab">
                        <div class="row" id="productGrid-<?php echo strtolower(str_replace(' ', '-', $category)); ?>">
                            <?php 
                            $categoryProducts = array_filter($products, function($product) use ($category) {
                                return $product['category'] === $category && $product['stock'] > 0;
                            });
                            foreach ($categoryProducts as $product): ?>
                            <div class="col-md-4 col-sm-6 mb-3 product-item" 
                                 data-name="<?php echo strtolower($product['name']); ?>"
                                 data-category="<?php echo strtolower($product['category']); ?>">
                                <div class="card h-100 product-card" 
                                     onclick="addToCart(<?php echo htmlspecialchars(json_encode($product)); ?>)"
                                     style="cursor: pointer; transition: all 0.3s;">
                                    <div class="card-body text-center">
                                        <h6 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h6>
                                        <p class="text-muted mb-1">
                                            <small><?php echo htmlspecialchars($product['category']); ?></small>
                                        </p>
                                        <p class="fw-bold mb-1">₱<?php echo number_format($product['price'], 2); ?></p>
                                        <p class="text-muted mb-0">
                                            <small>Stock: <?php echo $product['stock']; ?></small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Cart Section -->
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-cart me-2"></i>Shopping Cart
                </h5>
            </div>
            <div class="card-body">
                <div id="cartItems">
                    <p class="text-center text-muted py-4">Cart is empty</p>
                </div>
                
                <hr>
                
                <div class="d-flex justify-content-between mb-2">
                    <h6>Subtotal:</h6>
                    <h6 id="subtotal">₱0.00</h6>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <h5>Total:</h5>
                    <h5 id="total" class="text-primary">₱0.00</h5>
                </div>
                
                <div class="d-grid gap-2">
                    <button class="btn btn-danger" onclick="clearCart()" id="clearBtn" disabled>
                        <i class="bi bi-trash me-2"></i>Clear Cart
                    </button>
                    <button class="btn btn-primary btn-lg" onclick="processSale()" id="checkoutBtn" disabled>
                        <i class="bi bi-cash-coin me-2"></i>Process Sale
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Recent Sales -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-clock-history me-2"></i>Today's Sales
                </h6>
            </div>
            <div class="card-body">
                <?php
                $todaySales = getTodaySales();
                if (count($todaySales) > 0):
                ?>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Time</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($todaySales, 0, 5) as $sale): ?>
                            <tr>
                                <td>#<?php echo $sale['id']; ?></td>
                                <td><?php echo date('h:i A', strtotime($sale['sale_date'])); ?></td>
                                <td>₱<?php echo number_format($sale['total_amount'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-center text-muted mb-0">No sales today</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Hidden form for sale submission -->
<form id="saleForm" method="POST" action="">
    <input type="hidden" name="items" id="saleItems">
</form>

<!-- Confirm Modal -->
<div class="modal fade" id="confirmSaleModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-question-circle me-2"></i>Confirm Sale</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to process this sale?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="confirmProcessBtn"><i class="bi bi-cash-coin me-1"></i>Process</button>
      </div>
    </div>
  </div>
</div>

<!-- Processing Modal -->
<div class="modal fade" id="processingModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body text-center py-4">
        <div class="spinner-border text-primary mb-3" role="status"></div>
        <div class="fw-semibold">Processing sale...</div>
        <div class="text-muted small">Please wait</div>
      </div>
    </div>
  </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-check-circle-fill text-success me-2"></i>Sale Completed</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="mb-0">Sale has been recorded successfully.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="viewReceiptBtn"><i class="bi bi-receipt me-1"></i>View Receipt</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Receipt Modal -->
<div class="modal fade" id="receiptModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-receipt me-2"></i>Receipt</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="receiptContent">
        <!-- Receipt content will be loaded here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="printReceiptBtn"><i class="bi bi-printer me-1"></i>Print</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<style>
.product-card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,.1);
    transform: translateY(-2px);
}

.cart-item {
    border-bottom: 1px solid #eee;
    padding: 10px 0;
}

.cart-item:last-child {
    border-bottom: none;
}

/* Tab styling for light and dark mode visibility */
.nav-tabs {
    border-bottom: 2px solid var(--border-color);
}

.nav-tabs .nav-link {
    background-color: var(--card-bg);
    color: var(--text-color);
    border: 1px solid var(--border-color);
    border-bottom: none;
    margin-right: 8px;
    border-radius: 8px 8px 0 0;
    transition: all 0.3s ease;
}

.nav-tabs .nav-link:hover {
    background-color: var(--table-hover);
    color: var(--text-color);
    border-color: var(--coffee-primary);
}

.nav-tabs .nav-link.active {
    background-color: var(--coffee-primary);
    color: white;
    border-color: var(--coffee-primary);
    border-bottom-color: var(--card-bg);
}

.nav-tabs .nav-link.active .badge {
    background-color: rgba(255, 255, 255, 0.2) !important;
    color: white !important;
}

.nav-tabs .nav-link:not(.active) .badge {
    background-color: var(--coffee-secondary) !important;
    color: white !important;
}

/* Dark mode specific adjustments */
[data-theme="dark"] .nav-tabs .nav-link {
    background-color: var(--card-bg);
    color: var(--text-color);
    border-color: var(--border-color);
}

[data-theme="dark"] .nav-tabs .nav-link:hover {
    background-color: var(--table-hover);
    border-color: var(--coffee-primary);
}

[data-theme="dark"] .nav-tabs .nav-link.active {
    background-color: var(--coffee-primary);
    color: white;
    border-color: var(--coffee-primary);
}

[data-theme="dark"] .nav-tabs {
    border-bottom-color: var(--border-color);
}
</style>

<script>
let cart = [];

// Search products
document.getElementById('searchProduct').addEventListener('input', function(e) {
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
        document.getElementById('all-tab').click();
    }
});

// Add product to cart
function addToCart(product) {
    const existingItem = cart.find(item => item.product_id === product.id);
    
    if (existingItem) {
        if (existingItem.quantity < product.stock) {
            existingItem.quantity++;
        } else {
            alert('Cannot add more. Stock limit reached!');
            return;
        }
    } else {
        cart.push({
            product_id: product.id,
            name: product.name,
            price: parseFloat(product.price),
            quantity: 1,
            max_stock: product.stock
        });
    }
    
    updateCart();
}

// Remove item from cart
function removeFromCart(productId) {
    cart = cart.filter(item => item.product_id !== productId);
    updateCart();
}

// Update quantity
function updateQuantity(productId, quantity) {
    const item = cart.find(item => item.product_id === productId);
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
}

// Clear cart
function clearCart() {
    if (confirm('Are you sure you want to clear the cart?')) {
        cart = [];
        updateCart();
    }
}

// Update cart display
function updateCart() {
    const cartDiv = document.getElementById('cartItems');
    const clearBtn = document.getElementById('clearBtn');
    const checkoutBtn = document.getElementById('checkoutBtn');
    
    if (cart.length === 0) {
        cartDiv.innerHTML = '<p class="text-center text-muted py-4">Cart is empty</p>';
        clearBtn.disabled = true;
        checkoutBtn.disabled = true;
    } else {
        let html = '';
        let total = 0;
        
        cart.forEach(item => {
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
}

// Process sale with Bootstrap modals
function processSale() {
    if (cart.length === 0) {
        alert('Cart is empty!');
        return;
    }
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmSaleModal'));
    confirmModal.show();
    document.getElementById('confirmProcessBtn').onclick = () => {
        confirmModal.hide();
        new bootstrap.Modal(document.getElementById('processingModal')).show();
        document.getElementById('saleItems').value = JSON.stringify(cart);
        document.getElementById('saleForm').submit();
    };
}

// Show success modal on load if last_sale_id exists
<?php if (!empty($_SESSION['last_sale_id'])): ?>
window.addEventListener('DOMContentLoaded', () => {
    const saleId = <?php echo (int)$_SESSION['last_sale_id']; ?>;
    console.log('Success modal: Sale ID =', saleId);
    
    // Set up view receipt button
    const viewReceiptBtn = document.getElementById('viewReceiptBtn');
    if (viewReceiptBtn) {
        viewReceiptBtn.onclick = () => {
            console.log('View Receipt button clicked for sale ID:', saleId);
            loadReceiptModal(saleId);
        };
    } else {
        console.error('viewReceiptBtn not found');
    }
    
    // Set up print receipt button
    const printReceiptBtn = document.getElementById('printReceiptBtn');
    if (printReceiptBtn) {
        printReceiptBtn.onclick = () => {
            console.log('Print Receipt button clicked for sale ID:', saleId);
            window.open('receipt.php?id=' + saleId, '_blank');
        };
    }
    
    // Show the success modal
    const successModal = document.getElementById('successModal');
    if (successModal) {
        new bootstrap.Modal(successModal).show();
        console.log('Success modal shown');
    } else {
        console.error('successModal not found');
    }
});
<?php unset($_SESSION['last_sale_id']); endif; ?>

// Function to load receipt in modal
function loadReceiptModal(saleId) {
    console.log('loadReceiptModal called with saleId:', saleId);
    
    // Check if required elements exist
    const receiptContent = document.getElementById('receiptContent');
    const receiptModal = document.getElementById('receiptModal');
    
    if (!receiptContent) {
        console.error('receiptContent element not found');
        alert('Error: Receipt modal content area not found');
        return;
    }
    
    if (!receiptModal) {
        console.error('receiptModal element not found');
        alert('Error: Receipt modal not found');
        return;
    }
    
    // Show loading state
    receiptContent.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary mb-3" role="status"></div>
            <div class="fw-semibold">Loading receipt...</div>
        </div>
    `;
    
    // Show receipt modal
    const modal = new bootstrap.Modal(receiptModal);
    modal.show();
    
    // Load receipt content
    const url = `receipt.php?id=${saleId}&modal=true`;
    console.log('Fetching receipt from:', url);
    
    fetch(url)
        .then(response => {
            console.log('Receipt response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.text();
        })
        .then(html => {
            console.log('Receipt HTML received, length:', html.length);
            receiptContent.innerHTML = html;
            
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
            receiptContent.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Error loading receipt: ${error.message}<br>
                    <small>Sale ID: ${saleId}</small>
                </div>
            `;
        });
}
</script>

<?php require_once 'includes/footer.php'; ?>
