<?php
require_once '../inc/db.php';
include '../inc/header.php';

// Get selected category ID if any
$selected_category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

// Fetch all categories
$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

// Fetch products based on category
if($selected_category_id){
    $stmt = $pdo->prepare("SELECT p.*, c.name AS category_name 
                           FROM products p 
                           JOIN categories c ON p.category_id = c.id 
                           WHERE category_id=?");
    $stmt->execute([$selected_category_id]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $products = $pdo->query("SELECT p.*, c.name AS category_name 
                             FROM products p 
                             JOIN categories c ON p.category_id = c.id")->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="container">
    <!-- Sidebar Filter -->
    <aside class="sidebar">
        <h2>Categories</h2>
        <ul>
            <li><a href="index.php" class="<?php echo $selected_category_id==0?'active':''; ?>">All</a></li>
            <?php foreach($categories as $cat): ?>
                <li>
                    <a href="index.php?category_id=<?php echo $cat['id']; ?>" class="<?php echo $selected_category_id==$cat['id']?'active':''; ?>">
                        <?php echo $cat['name']; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </aside>

    <!-- Products Grid -->
    <section class="products-section">
        <h1>Products</h1>
        <div class="products-grid">
            <?php if(empty($products)): ?>
                <p>No products available.</p>
            <?php else: ?>
                <?php foreach($products as $product): ?>
                    <?php
                        // Get first image of product
                        $stmt_img = $pdo->prepare("SELECT image FROM product_images WHERE product_id=? LIMIT 1");
                        $stmt_img->execute([$product['id']]);
                        $img = $stmt_img->fetchColumn() ?: 'default.png';
                    ?>
                    <div class="product-card">
                        <a href="product.php?id=<?php echo $product['id']; ?>">
                            <img src="../uploads/<?php echo $img; ?>" alt="<?php echo $product['name']; ?>">
                            <h3><?php echo $product['name']; ?></h3>
                        </a>
                        <p class="category">Category: <?php echo $product['category_name']; ?></p>
                        <p class="price">₹<?php echo $product['price']; ?></p>
                        <p class="stock">Stock: <?php echo $product['stock']; ?></p>
                        <p class="size">Size: <?php echo $product['size']; ?></p>
                        <form method="post" action="cart.php">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <input type="number" name="quantity" value="1" min="1">
                            <button type="submit" name="add_to_cart">Add to Cart</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php include '../inc/footer.php'; ?>
