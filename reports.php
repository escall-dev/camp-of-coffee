<?php
// Include required files first (but not header yet)
require_once 'config/session.php';
requireLogin();
require_once 'includes/sales.php';

// Get date range
$startDate = $_GET['start_date'] ?? date('Y-m-01'); // First day of current month
$endDate = $_GET['end_date'] ?? date('Y-m-d'); // Today
$reportType = $_GET['report_type'] ?? 'sales';

// Get sales data
$sales = getSalesByDateRange($startDate, $endDate);

// Calculate totals
$totalSales = count($sales);
$totalRevenue = array_sum(array_column($sales, 'total_amount'));
$averageSale = $totalSales > 0 ? $totalRevenue / $totalSales : 0;

// Get best selling products for the selected date range (matches filters)
$bestProducts = getBestSellingProductsByRange($startDate, $endDate, 50);

// NOW include header after all processing is done
$pageTitle = 'Reports';
require_once 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col">
        <h2 class="mb-0">
            <i class="bi bi-graph-up me-2"></i>Reports
        </h2>
        <p class="text-muted">Sales and inventory reports</p>
    </div>
</div>

<!-- Report Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="" class="row g-3">
            <div class="col-md-3">
                <label for="report_type" class="form-label">Report Type</label>
                <select class="form-select" id="report_type" name="report_type">
                    <option value="sales" <?php echo $reportType === 'sales' ? 'selected' : ''; ?>>Sales Report</option>
                    <option value="products" <?php echo $reportType === 'products' ? 'selected' : ''; ?>>Product Report</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" class="form-control" id="start_date" name="start_date" 
                       value="<?php echo $startDate; ?>">
            </div>
            <div class="col-md-3">
                <label for="end_date" class="form-label">End Date</label>
                <input type="date" class="form-control" id="end_date" name="end_date" 
                       value="<?php echo $endDate; ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel me-2"></i>Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <h6 class="text-muted mb-2">Total Sales</h6>
                <h3 class="mb-0"><?php echo number_format($totalSales); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <h6 class="text-muted mb-2">Total Revenue</h6>
                <h3 class="mb-0">₱<?php echo number_format($totalRevenue, 2); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <h6 class="text-muted mb-2">Average Sale</h6>
                <h3 class="mb-0">₱<?php echo number_format($averageSale, 2); ?></h3>
            </div>
        </div>
    </div>
</div>

<?php if ($reportType === 'sales'): ?>
<!-- Sales Report -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="bi bi-cart-check me-2"></i>Sales Report
        </h5>
        <div>
            <button class="btn btn-success btn-sm" onclick="exportToCSV()">
                <i class="bi bi-file-earmark-excel me-1"></i>Export CSV
            </button>
            <button class="btn btn-danger btn-sm ms-1" onclick="exportToPDF()">
                <i class="bi bi-file-earmark-pdf me-1"></i>Export PDF
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="salesTable">
                <thead>
                    <tr>
                        <th>Sale ID</th>
                        <th>Date & Time</th>
                        <th>Cashier</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sales as $sale): ?>
                    <tr>
                        <td>#<?php echo $sale['id']; ?></td>
                        <td><?php echo date('M d, Y h:i A', strtotime($sale['sale_date'])); ?></td>
                        <td><?php echo htmlspecialchars($sale['username'] ?? 'Unknown'); ?></td>
                        <td>₱<?php echo number_format($sale['total_amount'], 2); ?></td>
                        <td>
                            <button class="btn btn-sm btn-info" 
                                    onclick="viewSaleDetails(<?php echo $sale['id']; ?>)">
                                <i class="bi bi-eye"></i> View
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php else: ?>
<!-- Product Report -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="bi bi-box-seam me-2"></i>Product Sales Report
        </h5>
        <div>
            <button class="btn btn-success btn-sm" onclick="exportProductsToCSV()">
                <i class="bi bi-file-earmark-excel me-1"></i>Export CSV
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="productsTable">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Units Sold</th>
                        <th>Times Sold</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bestProducts as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td>
                            <span class="badge bg-secondary">
                                <?php echo htmlspecialchars($product['category']); ?>
                            </span>
                        </td>
                        <td><?php echo number_format($product['total_quantity']); ?></td>
                        <td><?php echo number_format($product['times_sold']); ?></td>
                        <td>₱<?php echo number_format($product['total_revenue'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Sale Details Modal -->
<div class="modal fade" id="saleDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-receipt me-2"></i>Sale Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="saleDetailsContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
// View sale details
function viewSaleDetails(saleId) {
    fetch(`ajax/get_sale_details.php?id=${saleId}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('saleDetailsContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('saleDetailsModal')).show();
        });
}

// Export to CSV
function exportToCSV() {
    const table = document.getElementById('salesTable');
    let csv = 'Sale ID,Date Time,Cashier,Amount\n';
    
    const rows = table.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        csv += `${cells[0].textContent},${cells[1].textContent},${cells[2].textContent},${cells[3].textContent}\n`;
    });
    
    downloadCSV(csv, 'sales_report.csv');
}

// Export products to CSV
function exportProductsToCSV() {
    const table = document.getElementById('productsTable');
    let csv = 'Product,Category,Units Sold,Times Sold,Revenue\n';
    
    const rows = table.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        csv += `${cells[0].textContent},${cells[1].textContent},${cells[2].textContent},${cells[3].textContent},${cells[4].textContent}\n`;
    });
    
    downloadCSV(csv, 'products_report.csv');
}

// Download CSV helper
function downloadCSV(csv, filename) {
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    a.click();
    window.URL.revokeObjectURL(url);
}

// Export to PDF (simplified version)
function exportToPDF() {
    window.print();
}
</script>

<?php require_once 'includes/footer.php'; ?>
