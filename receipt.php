<?php
require_once 'config/session.php';
requireLogin();
require_once 'includes/sales.php';

$saleId = intval($_GET['id'] ?? 0);
$sale = $saleId ? getSaleById($saleId) : null;
if (!$sale) {
	echo '<p style="font-family:Arial;padding:20px">Receipt not found.</p>';
	exit;
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Receipt #<?php echo $sale['id']; ?> - Camp Of Coffee</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<style>
		body{font-family:Arial,Helvetica,sans-serif;background:#fff;margin:0}
		.receipt{max-width:360px;margin:10px auto;border:1px solid #ddd;padding:16px;border-radius:8px}
		.header{text-align:center}
		.header img{width:64px;height:64px;object-fit:cover;border-radius:50%}
		.store{font-weight:bold;margin-top:6px}
		.meta{font-size:12px;color:#555;margin-top:6px}
		h1{font-size:16px;margin:14px 0;border-top:1px dashed #ccc;border-bottom:1px dashed #ccc;padding:8px 0;text-align:center}
		table{width:100%;border-collapse:collapse;font-size:13px}
		td,th{padding:6px 4px}
		tfoot td{border-top:1px dashed #ccc;font-weight:bold}
		.actions{display:flex;gap:8px;justify-content:center;margin:16px;flex-wrap:wrap}
		.btn{padding:8px 12px;border-radius:6px;border:1px solid #8B4513;color:#fff;background:#8B4513;text-decoration:none;font-size:14px}
		.btn.secondary{background:#6F4E37;border-color:#6F4E37}
		.btn.gray{background:#495057;border-color:#495057}
		@media print{.actions{display:none}.receipt{border:none}}
	</style>
</head>
<body>
	<div class="receipt" id="receipt">
		<div class="header">
			<?php if (file_exists('assets/images/coc.jpg')): ?>
				<img src="assets/images/coc_logo.jpg" alt="Camp Of Coffee">
			<?php endif; ?>
			<div class="store">Camp Of Coffee</div>
			<div class="meta">
				Receipt #: <?php echo $sale['id']; ?> · 
				Date: <?php echo date('M d, Y h:i A', strtotime($sale['sale_date'])); ?>
			</div>
			<div class="meta">Cashier: <?php echo htmlspecialchars($sale['username'] ?? ''); ?></div>
		</div>

		<h1>Items</h1>
		<table id="itemsTable">
			<thead>
				<tr>
					<th align="left">Product</th>
					<th align="right">Qty</th>
					<th align="right">Price</th>
					<th align="right">Subtotal</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($sale['items'] as $it): ?>
				<tr>
					<td><?php echo htmlspecialchars($it['product_name']); ?></td>
					<td align="right"><?php echo (int)$it['quantity']; ?></td>
					<td align="right">₱<?php echo number_format($it['price'], 2); ?></td>
					<td align="right">₱<?php echo number_format($it['subtotal'], 2); ?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="3" align="right">Total</td>
					<td align="right">₱<?php echo number_format($sale['total_amount'], 2); ?></td>
				</tr>
			</tfoot>
		</table>
		<div class="meta" style="margin-top:10px;text-align:center">Thank you and enjoy your coffee!</div>
	</div>

	<div class="actions">
		<a class="btn" href="#" onclick="window.print();return false;">Print</a>
		<a class="btn secondary" id="pdfBtn" href="#">Download PDF</a>
		<a class="btn gray" id="imgBtn" href="#">Download Image</a>
		<a class="btn secondary" id="csvBtn" href="#">Export Excel (CSV)</a>
	</div>

	<!-- Libraries for PDF/PNG export -->
	<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
	<script>
	const receiptEl = document.getElementById('receipt');
	const saleId = <?php echo (int)$sale['id']; ?>;

	// Download PDF
	document.getElementById('pdfBtn').addEventListener('click', async (e) => {
		e.preventDefault();
		const canvas = await html2canvas(receiptEl, {scale: 2});
		const imgData = canvas.toDataURL('image/png');
		const { jsPDF } = window.jspdf;
		const pdf = new jsPDF({unit: 'pt', format: 'a4'});
		const pageWidth = pdf.internal.pageSize.getWidth();
		const ratio = canvas.height / canvas.width;
		const imgWidth = Math.min(pageWidth - 40, canvas.width);
		const imgHeight = imgWidth * ratio;
		pdf.addImage(imgData, 'PNG', 20, 20, imgWidth, imgHeight);
		pdf.save('receipt_' + saleId + '.pdf');
	});

	// Download Image (PNG)
	document.getElementById('imgBtn').addEventListener('click', async (e) => {
		e.preventDefault();
		const canvas = await html2canvas(receiptEl, {scale: 2});
		const url = canvas.toDataURL('image/png');
		const a = document.createElement('a');
		a.href = url;
		a.download = 'receipt_' + saleId + '.png';
		a.click();
	});

	// Export CSV (Excel compatible)
	document.getElementById('csvBtn').addEventListener('click', (e) => {
		e.preventDefault();
		let csv = 'Product,Qty,Price,Subtotal\n';
		const rows = document.querySelectorAll('#itemsTable tbody tr');
		rows.forEach(r => {
			const cells = r.querySelectorAll('td');
			const row = [cells[0].innerText, cells[1].innerText, cells[2].innerText.replace('₱',''), cells[3].innerText.replace('₱','')];
			csv += row.join(',') + '\n';
		});
		csv += `Total,,,${document.querySelector('#itemsTable tfoot td:last-child').innerText.replace('₱','')}\n`;
		const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
		const url = URL.createObjectURL(blob);
		const a = document.createElement('a');
		a.href = url;
		a.download = 'receipt_' + saleId + '.csv';
		a.click();
		URL.revokeObjectURL(url);
	});
	</script>
</body>
</html>
