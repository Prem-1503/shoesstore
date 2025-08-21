<?php
require_once '../inc/db.php';
session_start();
if(!isset($_SESSION['admin'])){ header("Location: login.php"); exit; }

$message = '';
$edit_product = null;

// Handle Add / Edit
if(isset($_POST['save'])){
    $id = $_POST['id'] ?? '';
    $category_id = $_POST['category_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $size = $_POST['size'];

    if($id){ // Update
        $stmt = $pdo->prepare("UPDATE products SET category_id=?, name=?, description=?, price=?, stock=?, size=? WHERE id=?");
        $stmt->execute([$category_id,$name,$description,$price,$stock,$size,$id]);
        $message = "Product updated successfully!";
    } else { // Insert
        $stmt = $pdo->prepare("INSERT INTO products (category_id,name,description,price,stock,size) VALUES (?,?,?,?,?,?)");
        $stmt->execute([$category_id,$name,$description,$price,$stock,$size]);
        $id = $pdo->lastInsertId();
        $message = "Product added successfully!";
    }

    // Handle multiple images upload
    if(isset($_FILES['images'])){
        foreach($_FILES['images']['name'] as $key => $img_name){
            if($img_name){
                $filename = time().'_'.basename($img_name);
                move_uploaded_file($_FILES['images']['tmp_name'][$key], '../uploads/'.$filename);
                $stmt_img = $pdo->prepare("INSERT INTO product_images (product_id, image) VALUES (?, ?)");
                $stmt_img->execute([$id, $filename]);
            }
        }
    }
}

// Delete Product
if(isset($_GET['delete'])){
    $stmt = $pdo->prepare("DELETE FROM products WHERE id=?");
    $stmt->execute([$_GET['delete']]);
    $message = "Product deleted successfully!";
}

// Edit Product: prefill form
if(isset($_GET['edit'])){
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $edit_product = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch products and categories
$products = $pdo->query("SELECT p.*, c.name as category_name 
                         FROM products p 
                         JOIN categories c ON p.category_id=c.id")->fetchAll(PDO::FETCH_ASSOC);

$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

include '../inc/header.php';
?>
<style>
    /* ===== Manage Products Page ===== */

.admin-container {
    max-width: 1200px;
    margin: 20px auto;
    padding: 20px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}

.success-msg {
    color: green;
    font-weight: bold;
    margin-bottom: 15px;
}

.form-container {
    margin-bottom: 40px;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 10px;
    background: #f9f9f9;
}

.form-container input,
.form-container select,
.form-container textarea {
    width: 100%;
    padding: 10px;
    margin: 8px 0;
    border-radius: 6px;
    border: 1px solid #ccc;
}

.form-container button.btn-save {
    background: #28a745;
    color: #fff;
    border: none;
    padding: 12px 20px;
    border-radius: 6px;
    cursor: pointer;
    transition: background 0.3s;
}

.form-container button.btn-save:hover {
    background: #218838;
}

/* Products Table */
.table-container {
    overflow-x: auto;
}

.products-table {
    width: 100%;
    border-collapse: collapse;
}

.products-table th,
.products-table td {
    padding: 12px 10px;
    border-bottom: 1px solid #ddd;
    text-align: center;
}

.products-table th {
    background: #007BFF;
    color: #fff;
}

.products-table tr:hover {
    background: #f1f1f1;
}

.products-table .thumb {
    width: 50px;
    height: 50px;
    object-fit: cover;
    margin: 2px;
    border-radius: 6px;
}

.delete-btn {
    background: #dc3545;
    color: #fff;
    padding: 6px 12px;
    border-radius: 6px;
    text-decoration: none;
    transition: background 0.3s;
}

.delete-btn:hover {
    background: #a71d2a;
}

/* Responsive */
@media (max-width: 768px) {
    .products-table th, .products-table td {
        font-size: 14px;
        padding: 8px;
    }

    .form-container input, .form-container select, .form-container textarea {
        font-size: 14px;
    }

    .form-container button {
        font-size: 14px;
    }
}
/* Edit & Delete Buttons */
.edit-btn {
    background: #ffc107;
    color: #fff;
    padding: 6px 12px;
    border-radius: 6px;
    text-decoration: none;
    margin-right: 5px;
    transition: background 0.3s;
}

.edit-btn:hover {
    background: #e0a800;
}

.btn-cancel {
    display: inline-block;
    padding: 8px 16px;
    background: #6c757d;
    color: #fff;
    border-radius: 6px;
    text-decoration: none;
    margin-left: 10px;
    transition: background 0.3s;
}

.btn-cancel:hover {
    background: #5a6268;
}

/* Alert handled via JS alert (already included) */

    </style>
<div class="admin-container">
    <h1>Manage Products</h1>

    <?php if($message): ?>
        <script>alert("<?php echo $message; ?>");</script>
    <?php endif; ?>

    <!-- Add / Edit Form -->
    <div class="form-container">
        <h2><?php echo $edit_product ? "Edit Product" : "Add Product"; ?></h2>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $edit_product['id'] ?? ''; ?>">
            <select name="category_id" required>
                <option value="">Select Category</option>
                <?php foreach($categories as $c): ?>
                    <option value="<?php echo $c['id']; ?>" 
                        <?php if($edit_product && $edit_product['category_id']==$c['id']) echo 'selected'; ?>>
                        <?php echo $c['name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="name" placeholder="Product Name" required value="<?php echo $edit_product['name'] ?? ''; ?>">
            <textarea name="description" placeholder="Description"><?php echo $edit_product['description'] ?? ''; ?></textarea>
            <input type="number" name="price" placeholder="Price" step="0.01" required value="<?php echo $edit_product['price'] ?? ''; ?>">
            <input type="number" name="stock" placeholder="Stock" required value="<?php echo $edit_product['stock'] ?? ''; ?>">
            <input type="text" name="size" placeholder="Size" value="<?php echo $edit_product['size'] ?? ''; ?>">
            <label>Upload Images (Multiple allowed):</label>
            <input type="file" name="images[]" multiple>
            <button type="submit" name="save" class="btn-save"><?php echo $edit_product ? "Update" : "Save"; ?></button>
            <?php if($edit_product): ?>
                <a href="manage_products.php" class="btn-cancel">Cancel</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Products Table -->
    <h2>Products List</h2>
    <div class="table-container">
    <table class="products-table">
        <thead>
        <tr>
            <th>ID</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Images</th><th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($products as $p): ?>
        <?php
            $stmt_img = $pdo->prepare("SELECT image FROM product_images WHERE product_id=?");
            $stmt_img->execute([$p['id']]);
            $imgs = $stmt_img->fetchAll(PDO::FETCH_COLUMN);
        ?>
        <tr>
            <td><?php echo $p['id']; ?></td>
            <td><?php echo $p['name']; ?></td>
            <td><?php echo $p['category_name']; ?></td>
            <td>₹<?php echo $p['price']; ?></td>
            <td><?php echo $p['stock']; ?></td>
            <td>
                <?php foreach($imgs as $img): ?>
                    <img src="../uploads/<?php echo $img; ?>" alt="img" class="thumb">
                <?php endforeach; ?>
            </td>
            <td>
                <a href="?edit=<?php echo $p['id']; ?>" class="edit-btn">Edit</a>
                <a href="?delete=<?php echo $p['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure to delete this product?');">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>

<?php include '../inc/footer.php'; ?>
