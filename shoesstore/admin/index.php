<?php
require_once '../inc/db.php';
session_start();
if(!isset($_SESSION['admin'])){
    header("Location: ../public/login.php");
    exit;
}

include '../inc/header.php';

// Fetch counts
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$total_categories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Shoe Buzz</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <style>
        /* ===== Admin Dashboard Styles ===== */
body {
    font-family: 'Arial', sans-serif;
    background: #f0f2f5;
    margin: 0;
    padding: 0;
}

.admin-dashboard {
    max-width: 1200px;
    margin: 40px auto;
    padding: 20px;
}

.admin-dashboard h1 {
    text-align: center;
    font-size: 32px;
    color: #333;
    margin-bottom: 40px;
}

/* Stats Cards */
.stats-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.card {
    background: #fff;
    border-radius: 12px;
    padding: 25px;
    text-align: center;
    border-left: 6px solid #007BFF;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transition: transform 0.3s, box-shadow 0.3s;
}

.card:hover {
    transform: translateY(-10px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.2);
}

.card .icon {
    font-size: 50px;
    margin-bottom: 15px;
}

.card h2 {
    margin-bottom: 10px;
    font-size: 20px;
    color: #555;
}

.card p {
    font-size: 28px;
    font-weight: bold;
    color: #222;
}

/* Admin Action Buttons */
.admin-actions {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 15px;
}

.admin-actions .btn {
    padding: 15px 30px;
    background: #007BFF;
    color: #fff;
    text-decoration: none;
    font-size: 16px;
    border-radius: 8px;
    transition: background 0.3s, transform 0.3s;
}

.admin-actions .btn:hover {
    background: #0056b3;
    transform: translateY(-3px);
}

.admin-actions .btn.logout {
    background: #dc3545;
}

.admin-actions .btn.logout:hover {
    background: #a71d2a;
}

/* Responsive */
@media (max-width: 768px) {
    .stats-cards {
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    }
}

        </style>
</head>
<body>

<div class="admin-dashboard">
    <h1>Welcome, <?php echo $_SESSION['admin']['name']; ?></h1>

    <!-- Stats Cards -->
    <div class="stats-cards">
        <div class="card users">
            <div class="icon">👥</div>
            <h2>Total Users</h2>
            <p><?php echo $total_users; ?></p>
        </div>
        <div class="card products">
            <div class="icon">👟</div>
            <h2>Total Products</h2>
            <p><?php echo $total_products; ?></p>
        </div>
        <div class="card orders">
            <div class="icon">🛒</div>
            <h2>Total Orders</h2>
            <p><?php echo $total_orders; ?></p>
        </div>
        <div class="card categories">
            <div class="icon">📂</div>
            <h2>Total Categories</h2>
            <p><?php echo $total_categories; ?></p>
        </div>
    </div>

    <!-- Admin Action Buttons -->
    <div class="admin-actions">
        <a href="manage_products.php" class="btn">Manage Products</a>
        <a href="manage_categories.php" class="btn">Manage Categories</a>
        <a href="manage_orders.php" class="btn">Manage Orders</a>
        <a href="manage_users.php" class="btn">Manage Users</a>
        <a href="logout.php" class="btn logout">Logout</a>
    </div>
</div>

<?php include '../inc/footer.php'; ?>
</body>
</html>
