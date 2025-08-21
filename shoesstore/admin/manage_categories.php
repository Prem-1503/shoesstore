<?php
require_once '../inc/db.php';
session_start();
if(!isset($_SESSION['admin'])){ header("Location: login.php"); exit; }

$message = '';
$edit_category = null;

// Handle Add / Edit
if(isset($_POST['save'])){
    $id = $_POST['id'] ?? '';
    $name = $_POST['name'];
    $description = $_POST['description'];

    if($id){ // Update
        $stmt = $pdo->prepare("UPDATE categories SET name=?, description=? WHERE id=?");
        $stmt->execute([$name,$description,$id]);
        $message = "Category updated successfully!";
    } else { // Insert
        $stmt = $pdo->prepare("INSERT INTO categories (name,description) VALUES (?,?)");
        $stmt->execute([$name,$description]);
        $message = "Category added successfully!";
    }
}

// Delete
if(isset($_GET['delete'])){
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id=?");
    $stmt->execute([$_GET['delete']]);
    $message = "Category deleted successfully!";
}

// Edit: prefill form
if(isset($_GET['edit'])){
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $edit_category = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch all categories
$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

include '../inc/header.php';
?>
<style>
    /* ===================== GENERAL ===================== */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f9;
    margin: 0;
    padding: 0;
    color: #333;
}

h1, h2 {
    color: #333;
    margin-bottom: 15px;
}

.admin-container {
    max-width: 900px;
    margin: 30px auto;
    padding: 20px;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

/* ===================== ALERT MESSAGE ===================== */
.alert {
    padding: 12px 20px;
    margin-bottom: 20px;
    border-radius: 5px;
    font-weight: bold;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

/* ===================== FORM ===================== */
form {
    margin-bottom: 30px;
}

form input[type="text"], form textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 12px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
    font-size: 14px;
}

form textarea {
    resize: vertical;
    min-height: 80px;
}

form button {
    padding: 10px 20px;
    background-color: #28a745;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 15px;
    transition: 0.3s;
}

form button:hover {
    background-color: #218838;
}

/* ===================== TABLE ===================== */
table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

table th, table td {
    padding: 12px 15px;
    border: 1px solid #ddd;
    text-align: left;
    font-size: 14px;
}

table th {
    background-color: #007bff;
    color: #fff;
    text-transform: uppercase;
}

table tr:nth-child(even) {
    background-color: #f9f9f9;
}

table tr:hover {
    background-color: #f1f1f1;
}

/* ===================== ACTION BUTTONS ===================== */
table a.delete-btn {
    padding: 5px 10px;
    background-color: #dc3545;
    color: #fff;
    border-radius: 4px;
    text-decoration: none;
    transition: 0.3s;
}

table a.delete-btn:hover {
    background-color: #c82333;
}

table a.edit-btn {
    padding: 5px 10px;
    background-color: #ffc107;
    color: #212529;
    border-radius: 4px;
    text-decoration: none;
    margin-right: 5px;
    transition: 0.3s;
}

table a.edit-btn:hover {
    background-color: #e0a800;
}

/* ===================== RESPONSIVE ===================== */
@media (max-width: 768px) {
    table, thead, tbody, th, td, tr {
        display: block;
    }

    table tr {
        margin-bottom: 15px;
    }

    table td {
        text-align: right;
        padding-left: 50%;
        position: relative;
    }

    table td::before {
        content: attr(data-label);
        position: absolute;
        left: 10px;
        width: 45%;
        padding-left: 15px;
        font-weight: bold;
        text-align: left;
    }

    table th {
        display: none;
    }
}

    </style>
<div class="admin-container">
    <h1>Manage Categories</h1>

    <?php if($message): ?>
        <script>alert("<?php echo $message; ?>");</script>
    <?php endif; ?>

    <!-- Add / Edit Form -->
    <div class="form-container">
        <h2><?php echo $edit_category ? "Edit Category" : "Add Category"; ?></h2>
        <form method="post">
            <input type="hidden" name="id" value="<?php echo $edit_category['id'] ?? ''; ?>">
            <input type="text" name="name" placeholder="Category Name" required value="<?php echo $edit_category['name'] ?? ''; ?>">
            <textarea name="description" placeholder="Description"><?php echo $edit_category['description'] ?? ''; ?></textarea>
            <button type="submit" name="save" class="btn-save"><?php echo $edit_category ? "Update" : "Save"; ?></button>
            <?php if($edit_category): ?>
                <a href="manage_categories.php" class="btn-cancel">Cancel</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Categories Table -->
    <h2>Categories List</h2>
    <div class="table-container">
        <table class="categories-table">
            <thead>
                <tr>
                    <th>ID</th><th>Name</th><th>Description</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($categories as $c): ?>
                <tr>
                    <td><?php echo $c['id']; ?></td>
                    <td><?php echo $c['name']; ?></td>
                    <td><?php echo $c['description']; ?></td>
                    <td>
                        <a href="?edit=<?php echo $c['id']; ?>" class="edit-btn">Edit</a>
                        <a href="?delete=<?php echo $c['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure to delete this category?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../inc/footer.php'; ?>
