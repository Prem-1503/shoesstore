<?php
// Password to hash
$password = "admin123";

// Generate hash
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Password: " . $password . "<br>";
echo "Hashed: " . $hash;
?>
