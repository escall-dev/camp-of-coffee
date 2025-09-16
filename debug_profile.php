<?php
require_once 'config/session.php';
requireLogin();
require_once 'includes/profile.php';

echo "<h2>Profile Debug Tool</h2>";
echo "<hr>";

$userId = getCurrentUserId();

// Test 1: Check if database has profile columns
echo "<h3>1. Database Schema Check</h3>";
try {
    require_once 'config/database.php';
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll();
    
    $profileColumns = ['full_name', 'email', 'phone', 'profile_image', 'updated_at'];
    $missingColumns = [];
    
    $existingColumns = array_column($columns, 'Field');
    
    foreach ($profileColumns as $col) {
        if (in_array($col, $existingColumns)) {
            echo "✅ Column exists: $col<br>";
        } else {
            echo "❌ Column missing: $col<br>";
            $missingColumns[] = $col;
        }
    }
    
    if (!empty($missingColumns)) {
        echo "<strong style='color: red;'>Missing columns found! Please run: update_database_profile.php</strong><br>";
    }
    
} catch(Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

// Test 2: Check current user profile
echo "<h3>2. Current User Profile</h3>";
echo "User ID: $userId<br>";
echo "Username: " . getCurrentUsername() . "<br>";

$user = getUserProfile($userId);
if ($user) {
    echo "✅ User profile loaded successfully<br>";
    echo "<pre>";
    print_r($user);
    echo "</pre>";
} else {
    echo "❌ Failed to load user profile<br>";
}

// Test 3: Test profile update function
echo "<h3>3. Test Profile Update</h3>";
if ($user) {
    // Test with sample data
    $testData = [
        'full_name' => 'Test Name',
        'email' => 'test@example.com',
        'phone' => '1234567890'
    ];
    
    echo "Testing update with data:<br>";
    echo "<pre>";
    print_r($testData);
    echo "</pre>";
    
    $result = updateUserProfile($userId, $testData);
    echo "Update result:<br>";
    echo "<pre>";
    print_r($result);
    echo "</pre>";
    
    if ($result['success']) {
        echo "✅ Profile update test successful<br>";
        
        // Revert the test changes
        $revertData = [
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'phone' => $user['phone']
        ];
        updateUserProfile($userId, $revertData);
        echo "✅ Test data reverted<br>";
    } else {
        echo "❌ Profile update test failed: " . $result['message'] . "<br>";
    }
}

// Test 4: Check POST data simulation
echo "<h3>4. POST Data Simulation</h3>";
echo "Simulating form submission...<br>";

// Simulate POST data
$_POST = [
    'action' => 'update_profile',
    'username' => getCurrentUsername(),
    'full_name' => 'Updated Name',
    'email' => 'updated@example.com',
    'phone' => '9876543210'
];

$data = [
    'full_name' => trim($_POST['full_name'] ?? ''),
    'email' => trim($_POST['email'] ?? ''),
    'phone' => trim($_POST['phone'] ?? ''),
    'username' => trim($_POST['username'] ?? '')
];

echo "POST data processed:<br>";
echo "<pre>";
print_r($data);
echo "</pre>";

$result = updateUserProfile($userId, $data);
echo "Simulation result:<br>";
echo "<pre>";
print_r($result);
echo "</pre>";

if ($result['success']) {
    echo "✅ Form simulation successful<br>";
    
    // Revert changes
    if ($user) {
        $revertData = [
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'phone' => $user['phone'],
            'username' => $user['username']
        ];
        updateUserProfile($userId, $revertData);
        echo "✅ Simulation data reverted<br>";
    }
} else {
    echo "❌ Form simulation failed: " . $result['message'] . "<br>";
}

// Clean up POST data
unset($_POST);

echo "<hr>";
echo "<h3>Troubleshooting Steps:</h3>";
echo "<ol>";
echo "<li>If columns are missing, run: <a href='update_database_profile.php'>update_database_profile.php</a></li>";
echo "<li>If profile loading fails, check database connection</li>";
echo "<li>If update tests fail, check error messages above</li>";
echo "<li>Try updating your profile at: <a href='profile.php'>profile.php</a></li>";
echo "</ol>";
?>

<style>
body { font-family: Arial, sans-serif; padding: 20px; }
pre { background: #f5f5f5; padding: 10px; border-radius: 5px; }
a { color: #8B4513; }
</style>
