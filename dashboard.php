<?php
// Include required files first (but not header yet)
require_once 'config/session.php';
requireLogin();
require_once 'config/database.php';

// Get today's sales
$todayDate = date('Y-m-d');
$stmt = $pdo->prepare("
    SELECT COUNT(*) as total_sales, COALESCE(SUM(total_amount), 0) as total_revenue 
    FROM sales 
    WHERE DATE(sale_date) = ?
");
$stmt->execute([$todayDate]);
$todaySales = $stmt->fetch();

// Get total products and low stock count
$stmt = $pdo->query("
    SELECT 
        COUNT(*) as total_products,
        SUM(CASE WHEN stock < 20 THEN 1 ELSE 0 END) as low_stock
    FROM products
");
$productStats = $stmt->fetch();

// Get total stock value
$stmt = $pdo->query("SELECT COALESCE(SUM(price * stock), 0) as stock_value FROM products");
$stockValue = $stmt->fetch();

// Get recent sales
$stmt = $pdo->query("
    SELECT s.id, s.sale_date, s.total_amount, u.username 
    FROM sales s
    LEFT JOIN users u ON s.cashier_id = u.id
    ORDER BY s.sale_date DESC
    LIMIT 10
");
$recentSales = $stmt->fetchAll();

// Get top selling products
$stmt = $pdo->query("
    SELECT p.name, SUM(si.quantity) as total_sold, SUM(si.subtotal) as revenue
    FROM sale_items si
    JOIN products p ON si.product_id = p.id
    GROUP BY p.id, p.name
    ORDER BY total_sold DESC
    LIMIT 5
");
$topProducts = $stmt->fetchAll();

// NOW include header after all processing is done
$pageTitle = 'Dashboard';
require_once 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col">
        <h2 class="mb-0">
            <i class="bi bi-speedometer2 me-2"></i>Dashboard
        </h2>
        <p class="text-muted">Welcome back, <?php echo htmlspecialchars(getCurrentUsername()); ?>!</p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Today's Sales</h6>
                        <h3 class="mb-0"><?php echo number_format($todaySales['total_sales']); ?></h3>
                    </div>
                    <div class="text-primary">
                        <i class="bi bi-cart-check-fill fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Today's Revenue</h6>
                        <h3 class="mb-0">₱<?php echo number_format($todaySales['total_revenue'], 2); ?></h3>
                    </div>
                    <div class="text-success">
                        <i class="bi bi-currency-exchange fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Total Products</h6>
                        <h3 class="mb-0"><?php echo number_format($productStats['total_products']); ?></h3>
                        <?php if ($productStats['low_stock'] > 0): ?>
                        <small class="text-danger">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <?php echo $productStats['low_stock']; ?> low stock
                        </small>
                        <?php endif; ?>
                    </div>
                    <div class="text-info">
                        <i class="bi bi-box-seam-fill fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Stock Value</h6>
                        <h3 class="mb-0">₱<?php echo number_format($stockValue['stock_value'], 2); ?></h3>
                    </div>
                    <div class="text-warning">
                        <i class="bi bi-graph-up-arrow fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Sales -->
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-clock-history me-2"></i>Recent Sales
                </h5>
            </div>
            <div class="card-body">
                <?php if (count($recentSales) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Sale ID</th>
                                <th>Date</th>
                                <th>Cashier</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentSales as $sale): ?>
                            <tr>
                                <td>#<?php echo $sale['id']; ?></td>
                                <td><?php echo date('M d, Y h:i A', strtotime($sale['sale_date'])); ?></td>
                                <td><?php echo htmlspecialchars($sale['username'] ?? 'Unknown'); ?></td>
                                <td>₱<?php echo number_format($sale['total_amount'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-3">
                    <a href="reports.php" class="btn btn-sm btn-primary">
                        View All Sales <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <?php else: ?>
                <p class="text-center text-muted py-4">No sales recorded yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Top Selling Products -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-trophy-fill me-2"></i>Top Products
                </h5>
            </div>
            <div class="card-body">
                <?php if (count($topProducts) > 0): ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($topProducts as $index => $product): ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0"><?php echo htmlspecialchars($product['name']); ?></h6>
                            <small class="text-muted">
                                Sold: <?php echo $product['total_sold']; ?> units
                            </small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-success">
                                ₱<?php echo number_format($product['revenue'], 2); ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p class="text-center text-muted py-4">No sales data available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-lightning-fill me-2"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <a href="sales.php" class="btn btn-primary w-100">
                            <i class="bi bi-cart-plus me-2"></i>New Sale
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="products.php" class="btn btn-success w-100">
                            <i class="bi bi-box-seam me-2"></i>Manage Products
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="reports.php" class="btn btn-info w-100">
                            <i class="bi bi-file-earmark-bar-graph me-2"></i>View Reports
                        </a>
                    </div>
                    <?php if (isAdmin()): ?>
                    <div class="col-md-3 mb-2">
                        <a href="users.php" class="btn btn-warning w-100">
                            <i class="bi bi-people me-2"></i>Manage Users
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
