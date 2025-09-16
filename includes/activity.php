<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

// Ensure table exists (safe if already created)
try {
	$pdo->exec("CREATE TABLE IF NOT EXISTS activity_logs (
		id INT AUTO_INCREMENT PRIMARY KEY,
		user_id INT NOT NULL,
		action VARCHAR(100) NOT NULL,
		details TEXT NULL,
		created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
		INDEX (user_id, created_at),
		FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
	)");
} catch (Throwable $e) {
	error_log('Ensure activity table error: ' . $e->getMessage());
}

function logActivity($userId, string $action, string $details = null): void {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, details) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $action, $details]);
    } catch (Throwable $e) {
        error_log('Activity log error: ' . $e->getMessage());
    }
}

function getMyActivity($userId, int $limit = 100) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT action, details, created_at FROM activity_logs WHERE user_id = ? ORDER BY created_at DESC LIMIT ?");
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    } catch (Throwable $e) {
        error_log('Get activity error: ' . $e->getMessage());
        return [];
    }
}
?>
