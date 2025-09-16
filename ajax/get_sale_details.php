<?php
require_once '../config/session.php';
require_once '../includes/sales.php';

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(401);
    echo 'Unauthorized';
    exit();
}

$saleId = intval($_GET['id'] ?? 0);

if (!$saleId) {
    echo 'Invalid sale ID';
    exit();
}

$sale = getSaleById($saleId);

if (!$sale) {
    echo 'Sale not found';
    exit();
}
?>

<div class="mb-3">
    <div class="row">
        <div class="col-md-6">
            <p><strong>Sale ID:</strong> #<?php echo $sale['id']; ?></p>
            <p><strong>Date:</strong> <?php echo date('M d, Y h:i A', strtotime($sale['sale_date'])); ?></p>
        </div>
        <div class="col-md-6">
            <p><strong>Cashier:</strong> <?php echo htmlspecialchars($sale['username'] ?? 'Unknown'); ?></p>
            <p><strong>Total Amount:</strong> ₱<?php echo number_format($sale['total_amount'], 2); ?></p>
        </div>
    </div>
</div>

<h6>Items Purchased:</h6>
<div class="table-responsive">
    <table class="table table-sm">
        <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sale['items'] as $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                <td>₱<?php echo number_format($item['price'], 2); ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td>₱<?php echo number_format($item['subtotal'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-end">Total:</th>
                <th>₱<?php echo number_format($sale['total_amount'], 2); ?></th>
            </tr>
        </tfoot>
    </table>
</div>
