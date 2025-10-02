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

<style>
.receipt-container {
    background: white;
    border-radius: 8px;
    padding: 20px;
    max-width: 400px;
    margin: 0 auto;
    font-family: Arial, sans-serif;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

/* Dark mode styles */
[data-theme="dark"] .receipt-container {
    background: #2d3748;
    color: #e2e8f0;
    box-shadow: 0 2px 10px rgba(0,0,0,0.3);
}

.receipt-header {
    text-align: center;
    margin-bottom: 20px;
}

.receipt-logo {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    margin: 0 auto 10px;
    display: block;
    object-fit: cover;
}

.receipt-title {
    font-size: 18px;
    font-weight: bold;
    color: #333;
    margin-bottom: 5px;
    transition: color 0.3s ease;
}

[data-theme="dark"] .receipt-title {
    color: #e2e8f0;
}

.receipt-details {
    font-size: 12px;
    color: #666;
    margin-bottom: 15px;
    transition: color 0.3s ease;
}

[data-theme="dark"] .receipt-details {
    color: #a0aec0;
}

.receipt-section {
    margin-bottom: 15px;
}

.receipt-items-header {
    font-size: 16px;
    font-weight: bold;
    color: #333;
    text-align: center;
    margin-bottom: 10px;
    border-top: 1px dashed #ccc;
    border-bottom: 1px dashed #ccc;
    padding: 8px 0;
    transition: all 0.3s ease;
}

[data-theme="dark"] .receipt-items-header {
    color: #e2e8f0;
    border-top: 1px dashed #4a5568;
    border-bottom: 1px dashed #4a5568;
}

.receipt-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

.receipt-table th {
    font-weight: bold;
    color: #333;
    padding: 6px 4px;
    text-align: left;
    transition: color 0.3s ease;
}

[data-theme="dark"] .receipt-table th {
    color: #e2e8f0;
}

.receipt-table th:nth-child(2),
.receipt-table th:nth-child(3),
.receipt-table th:nth-child(4) {
    text-align: center;
}

.receipt-table th:nth-child(4) {
    text-align: right;
}

.receipt-table td {
    padding: 6px 4px;
    color: #333;
    transition: color 0.3s ease;
}

[data-theme="dark"] .receipt-table td {
    color: #e2e8f0;
}

.receipt-table td:nth-child(2),
.receipt-table td:nth-child(3) {
    text-align: center;
}

.receipt-table td:nth-child(4) {
    text-align: right;
}

.receipt-total {
    border-top: 1px dashed #ccc;
    padding-top: 10px;
    margin-top: 10px;
    transition: border-color 0.3s ease;
}

[data-theme="dark"] .receipt-total {
    border-top: 1px dashed #4a5568;
}

.receipt-total-amount {
    font-size: 16px;
    font-weight: bold;
    color: #333;
    text-align: right;
    transition: color 0.3s ease;
}

[data-theme="dark"] .receipt-total-amount {
    color: #e2e8f0;
}

.receipt-footer {
    text-align: center;
    margin-top: 20px;
    font-size: 12px;
    color: #666;
    transition: color 0.3s ease;
}

[data-theme="dark"] .receipt-footer {
    color: #a0aec0;
}
</style>

<div class="receipt-container">
    <div class="receipt-header">
        <img src="assets/images/coc_logo.jpg" alt="Camp Of Coffee Logo" class="receipt-logo">
        <div class="receipt-title">Camp Of Coffee</div>
        <div class="receipt-details">
            Receipt #<?php echo $sale['id']; ?><br>
            <?php echo date('M d, Y h:i A', strtotime($sale['sale_date'])); ?><br>
            Cashier: <?php echo htmlspecialchars($sale['username'] ?? 'Unknown'); ?>
    </div>
</div>

    <div class="receipt-section">
        <div class="receipt-items-header">Items</div>
        <table class="receipt-table">
        <thead>
            <tr>
                <th>Product</th>
                    <th>Qty</th>
                <th>Price</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sale['items'] as $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                <td>₱<?php echo number_format($item['price'], 2); ?></td>
                <td>₱<?php echo number_format($item['subtotal'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>

    <div class="receipt-total">
        <div class="receipt-total-amount">
            Total: ₱<?php echo number_format($sale['total_amount'], 2); ?>
        </div>
    </div>

    <div class="receipt-footer">
        Thank you and enjoy your coffee!
    </div>
</div>

<div class="mt-3 text-center">
    <button class="btn btn-success btn-sm me-2" onclick="exportReceiptToPDF(<?php echo $sale['id']; ?>)">
        <i class="bi bi-file-earmark-pdf me-1"></i>Export PDF
    </button>
    <button class="btn btn-primary btn-sm" onclick="exportReceiptToJPG(<?php echo $sale['id']; ?>)">
        <i class="bi bi-image me-1"></i>Export JPG
    </button>
</div>

<script>
function exportReceiptToPDF(saleId) {
    // Create a new window with the receipt content for printing/PDF
    const receiptContent = document.querySelector('.receipt-container').outerHTML;
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
            ${receiptContent}
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.focus();
    setTimeout(() => {
        printWindow.print();
    }, 500);
}

function exportReceiptToJPG(saleId) {
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
}

function captureReceiptAsJPG(saleId) {
    const receiptElement = document.querySelector('.receipt-container');
    
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
</script>
