<?php
require_once '../inc/db.php';
session_start();
if(!isset($_SESSION['admin'])){ header("Location: login.php"); exit; }

if(isset($_GET['update']) && isset($_GET['status'])){
    $stmt = $pdo->prepare("UPDATE orders SET status=? WHERE id=?");
    $stmt->execute([$_GET['status'], $_GET['update']]);
}

$orders = $pdo->query("SELECT o.*, u.name as user_name FROM orders o JOIN users u ON o.user_id=u.id ORDER BY o.created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
include '../inc/header.php';
?>

<h1>Manage Orders</h1>

<style>
/* ========== GENERAL ========== */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f9;
    color: #333;
}

h1 {
    text-align: center;
    margin-bottom: 20px;
}

/* ========== TABLE ========== */
table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

th, td {
    padding: 12px 15px;
    border-bottom: 1px solid #ddd;
    text-align: left;
    font-size: 14px;
}

th {
    background-color: #007bff;
    color: #fff;
    text-transform: uppercase;
}

tr:nth-child(even) {
    background-color: #f9f9f9;
}

tr:hover {
    background-color: #f1f1f1;
}

/* ========== ACTION BUTTONS ========== */
a.status-btn {
    padding: 5px 10px;
    margin: 2px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 12px;
    color: #fff;
    transition: 0.3s;
}

a.Pending { background-color: #6c757d; }
a.Pending:hover { background-color: #5a6268; }

a.Processing { background-color: #17a2b8; }
a.Processing:hover { background-color: #138496; }

a.Shipped { background-color: #ffc107; color:#212529; }
a.Shipped:hover { background-color: #e0a800; }

a.Delivered { background-color: #28a745; }
a.Delivered:hover { background-color: #218838; }

a.Canceled { background-color: #dc3545; }
a.Canceled:hover { background-color: #c82333; }

/* ========== RESPONSIVE ========== */
@media (max-width: 768px) {
    table, thead, tbody, th, td, tr {
        display: block;
    }

    table tr {
        margin-bottom: 15px;
        border-bottom: 2px solid #ddd;
    }

    table td {
        text-align: right;
        padding-left: 50%;
        position: relative;
    }

    table td::before {
        content: attr(data-label);
        position: absolute;
        left: 15px;
        width: 45%;
        font-weight: bold;
        text-align: left;
    }

    table th {
        display: none;
    }
}
</style>

<table>
    <tr>
        <th>ID</th>
        <th>User</th>
        <th>Total</th>
        <th>Status</th>
        <th>Date</th>
        <th>Actions</th>
    </tr>
    <?php foreach($orders as $o): ?>
    <tr>
        <td data-label="ID"><?php echo $o['id']; ?></td>
        <td data-label="User"><?php echo $o['user_name']; ?></td>
        <td data-label="Total">₹<?php echo $o['total_price']; ?></td>
        <td data-label="Status"><?php echo $o['status']; ?></td>
        <td data-label="Date"><?php echo $o['created_at']; ?></td>
        <td data-label="Actions">
            <a class="status-btn Pending" href="?update=<?php echo $o['id']; ?>&status=Pending">Pending</a>
            <a class="status-btn Processing" href="?update=<?php echo $o['id']; ?>&status=Processing">Processing</a>
            <a class="status-btn Shipped" href="?update=<?php echo $o['id']; ?>&status=Shipped">Shipped</a>
            <a class="status-btn Delivered" href="?update=<?php echo $o['id']; ?>&status=Delivered">Delivered</a>
            <a class="status-btn Canceled" href="?update=<?php echo $o['id']; ?>&status=Canceled">Canceled</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php include '../inc/footer.php'; ?>
