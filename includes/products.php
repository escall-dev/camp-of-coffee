<?php
require_once __DIR__ . '/../config/database.php';

// Get all products
function getAllProducts() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("
            SELECT id, name, category, price, stock, created_at, updated_at 
            FROM products 
            ORDER BY name ASC
        ");
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        error_log("Get products error: " . $e->getMessage());
        return [];
    }
}

// Get product by ID
function getProductById($id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch(PDOException $e) {
        error_log("Get product error: " . $e->getMessage());
        return null;
    }
}

// Create new product
function createProduct($name, $category, $price, $stock) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO products (name, category, price, stock) 
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([$name, $category, $price, $stock]);
    } catch(PDOException $e) {
        error_log("Create product error: " . $e->getMessage());
        return false;
    }
}

// Update product
function updateProduct($id, $name, $category, $price, $stock) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            UPDATE products 
            SET name = ?, category = ?, price = ?, stock = ? 
            WHERE id = ?
        ");
        return $stmt->execute([$name, $category, $price, $stock, $id]);
    } catch(PDOException $e) {
        error_log("Update product error: " . $e->getMessage());
        return false;
    }
}

// Delete product
function deleteProduct($id) {
    global $pdo;
    
    try {
        // Check if product has sales
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM sale_items WHERE product_id = ?");
        $stmt->execute([$id]);
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            return ['success' => false, 'message' => 'Cannot delete product with existing sales records.'];
        }
        
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $result = $stmt->execute([$id]);
        
        return ['success' => $result, 'message' => $result ? 'Product deleted successfully.' : 'Failed to delete product.'];
    } catch(PDOException $e) {
        error_log("Delete product error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Database error occurred.'];
    }
}

// Update product stock
function updateProductStock($id, $quantity, $operation = 'subtract') {
    global $pdo;
    
    try {
        if ($operation === 'subtract') {
            $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");
            return $stmt->execute([$quantity, $id, $quantity]);
        } else {
            $stmt = $pdo->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
            return $stmt->execute([$quantity, $id]);
        }
    } catch(PDOException $e) {
        error_log("Update stock error: " . $e->getMessage());
        return false;
    }
}

// Get low stock products
function getLowStockProducts($threshold = 20) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT id, name, category, stock 
            FROM products 
            WHERE stock < ? 
            ORDER BY stock ASC
        ");
        $stmt->execute([$threshold]);
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        error_log("Get low stock error: " . $e->getMessage());
        return [];
    }
}

// Get product categories
function getProductCategories() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT DISTINCT category FROM products ORDER BY category");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch(PDOException $e) {
        error_log("Get categories error: " . $e->getMessage());
        return [];
    }
}
?>
