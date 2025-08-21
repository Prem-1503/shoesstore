<?php
require_once '../inc/db.php';
session_start();
if(!isset($_SESSION['admin'])){ header("Location: login.php"); exit; }

if(isset($_GET['delete'])){
    $stmt = $pdo->prepare("DELETE FROM users WHERE id=?");
    $stmt->execute([$_GET['delete']]);
}

$users = $pdo->query("SELECT * FROM users WHERE role='user'")->fetchAll(PDO::FETCH_ASSOC);
include '../inc/header.php';
?>

<h1>Manage Users</h1>

<style>
/* ======= GENERAL ======= */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f9;
    color: #333;
}

h1 {
    text-align: center;
    margin-bottom: 20px;
}

/* ======= TABLE ======= */
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

/* ======= DELETE BUTTON ======= */
a.delete-btn {
    padding: 6px 12px;
    background-color: #dc3545;
    color: #fff;
    text-decoration: none;
    border-radius: 4px;
    font-size: 13px;
    transition: 0.3s;
}

a.delete-btn:hover {
    background-color: #c82333;
}

/* ======= RESPONSIVE ======= */
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
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Actions</th>
    </tr>
    <?php foreach($users as $u): ?>
    <tr>
        <td data-label="ID"><?php echo $u['id']; ?></td>
        <td data-label="Name"><?php echo $u['name']; ?></td>
        <td data-label="Email"><?php echo $u['email']; ?></td>
        <td data-label="Phone"><?php echo $u['phone']; ?></td>
        <td data-label="Actions">
            <a class="delete-btn" href="?delete=<?php echo $u['id']; ?>" onclick="return confirm('Are you sure to delete this user?');">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php include '../inc/footer.php'; ?>
