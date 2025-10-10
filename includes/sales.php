<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/products.php';
require_once __DIR__ . '/activity.php';

// Create new sale
function createSale($items, $cashierId) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Calculate total amount
        $totalAmount = 0;
        foreach ($items as $item) {
            $totalAmount += $item['price'] * $item['quantity'];
        }
        
        // Insert sale record
        $stmt = $pdo->prepare("INSERT INTO sales (total_amount, cashier_id) VALUES (?, ?)");
        $stmt->execute([$totalAmount, $cashierId]);
        $saleId = $pdo->lastInsertId();
        
        // Insert sale items and update stock
        $stmt = $pdo->prepare("
            INSERT INTO sale_items (sale_id, product_id, quantity, price, subtotal) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        foreach ($items as $item) {
            $subtotal = $item['price'] * $item['quantity'];
            $stmt->execute([
                $saleId, 
                $item['product_id'], 
                $item['quantity'], 
                $item['price'], 
                $subtotal
            ]);
            
            // Update product stock
            if (!updateProductStock($item['product_id'], $item['quantity'], 'subtract')) {
                throw new Exception("Insufficient stock for product ID: " . $item['product_id']);
            }
        }
        
        $pdo->commit();
        // activity
        logActivity($cashierId, 'sale', 'Created sale #' . $saleId . ' total ₱' . number_format($totalAmount, 2));
        return ['success' => true, 'sale_id' => $saleId];
        
    } catch(Exception $e) {
        $pdo->rollBack();
        error_log("Create sale error: " . $e->getMessage());
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

// Get sale by ID with items
function getSaleById($id) {
    global $pdo;
    
    try {
        error_log("getSaleById: Looking for sale ID $id");
        
        // Get sale info
        $stmt = $pdo->prepare("
            SELECT s.*, u.username 
            FROM sales s 
            LEFT JOIN users u ON s.cashier_id = u.id 
            WHERE s.id = ?
        ");
        $stmt->execute([$id]);
        $sale = $stmt->fetch();
        
        if ($sale) {
            error_log("getSaleById: Sale found, getting items");
            // Get sale items
            $stmt = $pdo->prepare("
                SELECT si.*, p.name as product_name 
                FROM sale_items si 
                JOIN products p ON si.product_id = p.id 
                WHERE si.sale_id = ?
            ");
            $stmt->execute([$id]);
            $sale['items'] = $stmt->fetchAll();
            
            error_log("getSaleById: Found " . count($sale['items']) . " items for sale $id");
        } else {
            error_log("getSaleById: No sale found for ID $id");
        }
        
        return $sale;
    } catch(PDOException $e) {
        error_log("Get sale error: " . $e->getMessage());
        return null;
    }
}

// Normalize dates to full-day bounds (inclusive)
function normalizeDateRange(string $startDate, string $endDate): array {
    $start = new DateTime($startDate . ' 00:00:00');
    $end = new DateTime($endDate . ' 23:59:59');
    if ($start > $end) {
        [$start, $end] = [$end, $start];
    }
    return [$start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s')];
}

// Get sales by date range (inclusive)
function getSalesByDateRange($startDate, $endDate) {
    global $pdo;
    
    try {
        [$startTs, $endTs] = normalizeDateRange($startDate, $endDate);
        $stmt = $pdo->prepare("
            SELECT s.*, u.username 
            FROM sales s 
            LEFT JOIN users u ON s.cashier_id = u.id 
            WHERE s.sale_date BETWEEN ? AND ? 
            ORDER BY s.sale_date DESC
        ");
        $stmt->execute([$startTs, $endTs]);
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        error_log("Get sales by date error: " . $e->getMessage());
        return [];
    }
}

// Get today's sales
function getTodaySales() {
    $today = date('Y-m-d');
    return getSalesByDateRange($today, $today);
}

// Get sales summary by period
function getSalesSummary($period = 'today') {
    global $pdo;
    
    $dateCondition = "";
    switch ($period) {
        case 'today':
            $dateCondition = "DATE(sale_date) = CURDATE()";
            break;
        case 'week':
            $dateCondition = "YEARWEEK(sale_date) = YEARWEEK(CURDATE())";
            break;
        case 'month':
            $dateCondition = "MONTH(sale_date) = MONTH(CURDATE()) AND YEAR(sale_date) = YEAR(CURDATE())";
            break;
        case 'year':
            $dateCondition = "YEAR(sale_date) = YEAR(CURDATE())";
            break;
    }
    
    try {
        $stmt = $pdo->prepare("
            SELECT 
                COUNT(*) as total_sales,
                COALESCE(SUM(total_amount), 0) as total_revenue,
                COALESCE(AVG(total_amount), 0) as average_sale
            FROM sales 
            WHERE $dateCondition
        ");
        $stmt->execute();
        return $stmt->fetch();
    } catch(PDOException $e) {
        error_log("Get sales summary error: " . $e->getMessage());
        return null;
    }
}

// Get best selling products
function getBestSellingProducts($limit = 10, $period = 'all') {
    global $pdo;
    
    $dateCondition = "";
    if ($period !== 'all') {
        switch ($period) {
            case 'today':
                $dateCondition = "AND DATE(s.sale_date) = CURDATE()";
                break;
            case 'week':
                $dateCondition = "AND YEARWEEK(s.sale_date) = YEARWEEK(CURDATE())";
                break;
            case 'month':
                $dateCondition = "AND MONTH(s.sale_date) = MONTH(CURDATE()) AND YEAR(s.sale_date) = YEAR(CURDATE())";
                break;
        }
    }
    
    try {
        $stmt = $pdo->prepare("
            SELECT 
                p.id,
                p.name,
                p.category,
                SUM(si.quantity) as total_quantity,
                SUM(si.subtotal) as total_revenue,
                COUNT(DISTINCT si.sale_id) as times_sold
            FROM sale_items si
            JOIN products p ON si.product_id = p.id
            JOIN sales s ON si.sale_id = s.id
            WHERE 1=1 $dateCondition
            GROUP BY p.id, p.name, p.category
            ORDER BY total_quantity DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        error_log("Get best selling products error: " . $e->getMessage());
        return [];
    }
}

// Get best selling products within an arbitrary date range (inclusive)
function getBestSellingProductsByRange(string $startDate, string $endDate, int $limit = 10) {
    global $pdo;
    try {
        [$startTs, $endTs] = normalizeDateRange($startDate, $endDate);
        $stmt = $pdo->prepare("
            SELECT 
                p.id,
                p.name,
                p.category,
                SUM(si.quantity) as total_quantity,
                SUM(si.subtotal) as total_revenue,
                COUNT(DISTINCT si.sale_id) as times_sold
            FROM sale_items si
            JOIN products p ON si.product_id = p.id
            JOIN sales s ON si.sale_id = s.id
            WHERE s.sale_date BETWEEN ? AND ?
            GROUP BY p.id, p.name, p.category
            ORDER BY total_quantity DESC
            LIMIT ?
        ");
        $stmt->execute([$startTs, $endTs, $limit]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Get best selling by range error: " . $e->getMessage());
        return [];
    }
}

// Void/Cancel sale
function voidSale($saleId) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Get sale items to restore stock
        $stmt = $pdo->prepare("SELECT product_id, quantity FROM sale_items WHERE sale_id = ?");
        $stmt->execute([$saleId]);
        $items = $stmt->fetchAll();
        
        // Restore stock for each item
        foreach ($items as $item) {
            updateProductStock($item['product_id'], $item['quantity'], 'add');
        }
        
        // Delete sale items
        $stmt = $pdo->prepare("DELETE FROM sale_items WHERE sale_id = ?");
        $stmt->execute([$saleId]);
        
        // Delete sale
        $stmt = $pdo->prepare("DELETE FROM sales WHERE id = ?");
        $stmt->execute([$saleId]);
        
        $pdo->commit();
        return true;
        
    } catch(PDOException $e) {
        $pdo->rollBack();
        error_log("Void sale error: " . $e->getMessage());
        return false;
    }
}
?>
