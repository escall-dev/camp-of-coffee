<?php
// Include required files first (but not header yet)
require_once 'config/session.php';
requireLogin();
require_once 'includes/products.php';

// Handle form submissions
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $name = trim($_POST['name'] ?? '');
                $category = trim($_POST['category'] ?? '');
                $price = floatval($_POST['price'] ?? 0);
                $stock = intval($_POST['stock'] ?? 0);
                
                if ($name && $category && $price > 0) {
                    if (createProduct($name, $category, $price, $stock)) {
                        $message = 'Product created successfully!';
                        $messageType = 'success';
                    } else {
                        $message = 'Failed to create product.';
                        $messageType = 'danger';
                    }
                } else {
                    $message = 'Please fill all required fields.';
                    $messageType = 'warning';
                }
                break;
                
            case 'update':
                $id = intval($_POST['id'] ?? 0);
                $name = trim($_POST['name'] ?? '');
                $category = trim($_POST['category'] ?? '');
                $price = floatval($_POST['price'] ?? 0);
                $stock = intval($_POST['stock'] ?? 0);
                
                if ($id && $name && $category && $price > 0) {
                    if (updateProduct($id, $name, $category, $price, $stock)) {
                        $message = 'Product updated successfully!';
                        $messageType = 'success';
                    } else {
                        $message = 'Failed to update product.';
                        $messageType = 'danger';
                    }
                }
                break;
                
            case 'delete':
                $id = intval($_POST['id'] ?? 0);
                if ($id) {
                    $result = deleteProduct($id);
                    $message = $result['message'];
                    $messageType = $result['success'] ? 'success' : 'danger';
                }
                break;
        }
    }
}

// Get all products
$products = getAllProducts();
$categories = getProductCategories();

// NOW include header after all processing is done
$pageTitle = 'Products';
require_once 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col">
        <h2 class="mb-0">
            <i class="bi bi-box-seam me-2"></i>Products Management
        </h2>
    </div>
    <div class="col-auto me-5">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
            <i class="bi bi-plus-circle me-2"></i>Add New Product
        </button>
    </div>
</div>

<?php if ($message): ?>
<div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
    <i class="bi bi-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>-fill me-2"></i>
    <?php echo htmlspecialchars($message); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo $product['id']; ?></td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td>
                            <span class="badge bg-secondary">
                                <?php echo htmlspecialchars($product['category']); ?>
                            </span>
                        </td>
                        <td>₱<?php echo number_format($product['price'], 2); ?></td>
                        <td>
                            <?php if ($product['stock'] < 20): ?>
                                <span class="text-danger fw-bold">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                    <?php echo $product['stock']; ?>
                                </span>
                            <?php else: ?>
                                <?php echo $product['stock']; ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($product['stock'] == 0): ?>
                                <span class="badge bg-danger">Out of Stock</span>
                            <?php elseif ($product['stock'] < 20): ?>
                                <span class="badge bg-warning text-dark">Low Stock</span>
                            <?php else: ?>
                                <span class="badge bg-success">In Stock</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-info" 
                                    onclick="editProduct(<?php echo htmlspecialchars(json_encode($product)); ?>)">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" 
                                    onclick="deleteProduct(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['name']); ?>')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="">
                <input type="hidden" name="action" value="create">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-plus-circle me-2"></i>Add New Product
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" id="category" name="category" required>
                            <option value="" disabled selected>Select a category</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>">
                                <?php echo htmlspecialchars($cat); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Price (₱)</label>
                        <input type="number" class="form-control" id="price" name="price" 
                               min="0" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="stock" class="form-label">Initial Stock</label>
                        <input type="number" class="form-control" id="stock" name="stock" 
                               min="0" value="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Save Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-pencil me-2"></i>Edit Product
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_category" class="form-label">Category</label>
                        <select class="form-select" id="edit_category" name="category" required>
                            <option value="" disabled>Select a category</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>">
                                <?php echo htmlspecialchars($cat); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_price" class="form-label">Price (₱)</label>
                        <input type="number" class="form-control" id="edit_price" name="price" 
                               min="0" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_stock" class="form-label">Stock</label>
                        <input type="number" class="form-control" id="edit_stock" name="stock" 
                               min="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Update Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteProductModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" id="delete_id">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>Confirm Delete
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this product?</p>
                    <p class="fw-bold mb-0">Product: <span id="delete_name"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-2"></i>Delete Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editProduct(product) {
    document.getElementById('edit_id').value = product.id;
    document.getElementById('edit_name').value = product.name;
    document.getElementById('edit_category').value = product.category;
    document.getElementById('edit_price').value = product.price;
    document.getElementById('edit_stock').value = product.stock;
    
    new bootstrap.Modal(document.getElementById('editProductModal')).show();
}

function deleteProduct(id, name) {
    document.getElementById('delete_id').value = id;
    document.getElementById('delete_name').textContent = name;
    
    new bootstrap.Modal(document.getElementById('deleteProductModal')).show();
}
</script>

<?php require_once 'includes/footer.php'; ?>
