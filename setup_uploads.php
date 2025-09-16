<?php
echo "<h2>Setting up Upload Directories</h2>";

// Create upload directories
$directories = [
    'uploads',
    'uploads/profiles'
];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "✅ Created directory: $dir<br>";
        } else {
            echo "❌ Failed to create directory: $dir<br>";
        }
    } else {
        echo "ℹ️ Directory already exists: $dir<br>";
    }
}

// Create .htaccess file to protect uploads directory
$htaccessContent = "Options -Indexes\n";
$htaccessContent .= "# Allow only image files\n";
$htaccessContent .= "<FilesMatch \"\\.(jpg|jpeg|png|gif)$\">\n";
$htaccessContent .= "    Order allow,deny\n";
$htaccessContent .= "    Allow from all\n";
$htaccessContent .= "</FilesMatch>\n";
$htaccessContent .= "<FilesMatch \"\\.(php|html|htm|js)$\">\n";
$htaccessContent .= "    Order deny,allow\n";
$htaccessContent .= "    Deny from all\n";
$htaccessContent .= "</FilesMatch>\n";

if (file_put_contents('uploads/.htaccess', $htaccessContent)) {
    echo "✅ Created security .htaccess file<br>";
} else {
    echo "❌ Failed to create .htaccess file<br>";
}

echo "<br><h3>✅ Upload setup completed!</h3>";
echo "<p>Profile image uploads are now ready to use.</p>";
echo "<p><a href='dashboard.php' class='btn btn-primary'>Go to Dashboard</a></p>";
?>

<style>
.btn {
    display: inline-block;
    padding: 10px 20px;
    background: #8B4513;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    margin-top: 10px;
}
</style>
