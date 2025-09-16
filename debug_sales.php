<?php
require_once 'config/session.php';
requireLogin();
require_once 'config/database.php';

echo '<h2>Last 50 sales (raw)</h2>';
try {
	$stmt = $pdo->query("SELECT id, sale_date, total_amount, cashier_id FROM sales ORDER BY sale_date DESC LIMIT 50");
	$rows = $stmt->fetchAll();
	if (!$rows) {
		echo '<p>No sales found.</p>';
	} else {
		echo '<table border="1" cellpadding="6" cellspacing="0">';
		echo '<tr><th>ID</th><th>sale_date (DB)</th><th>Total</th><th>Cashier</th></tr>';
		foreach ($rows as $r) {
			echo '<tr>';
			echo '<td>#'.htmlspecialchars($r['id']).'</td>';
			echo '<td>'.htmlspecialchars($r['sale_date']).'</td>';
			echo '<td>'.number_format((float)$r['total_amount'],2).'</td>';
			echo '<td>'.htmlspecialchars((string)$r['cashier_id']).'</td>';
			echo '</tr>';
		}
		echo '</table>';
	}
} catch (Throwable $e) {
	echo '<pre>Error: '.htmlspecialchars($e->getMessage()).'</pre>';
}

?>
<style>body{font-family:Arial,Helvetica,sans-serif;padding:20px}</style>
