<?php
require_once '../inc/db.php';
include '../inc/header.php';

if(!isset($_GET['id'])) { 
    echo "<p>Product not found!</p>"; 
    include '../inc/footer.php';
    exit; 
}

$id = $_GET['id'];

// Fetch product with category name
$stmt = $pdo->prepare("SELECT p.*, c.name AS category_name 
                       FROM products p 
                       JOIN categories c ON p.category_id = c.id 
                       WHERE p.id=?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$product){ 
    echo "<p>Product not found!</p>"; 
    include '../inc/footer.php';
    exit; 
}

// Fetch all images for the product
$stmt_images = $pdo->prepare("SELECT image FROM product_images WHERE product_id=?");
$stmt_images->execute([$id]);
$images = $stmt_images->fetchAll(PDO::FETCH_COLUMN);
if(empty($images)){
    $images = ['default.png']; // fallback image
}
?>

<div class="product-detail-container">

    <!-- Image Slider -->
    <div class="product-image-slider">
        <?php foreach($images as $index => $img): ?>
            <div class="slide <?php echo $index === 0 ? 'active' : ''; ?>">
                <img src="../uploads/<?php echo trim($img); ?>" alt="<?php echo $product['name']; ?>">
            </div>
        <?php endforeach; ?>
        <button class="prev">&#10094;</button>
        <button class="next">&#10095;</button>
    </div>

    <!-- Product Info -->
    <div class="product-info">
        <h1><?php echo $product['name']; ?></h1>
        <p class="category">Category: <?php echo $product['category_name']; ?></p>
        <p class="price">₹<?php echo $product['price']; ?></p>
        <p class="stock">Stock: <?php echo $product['stock']; ?></p>
        <p class="size">Size: <?php echo $product['size']; ?></p>
        <p class="quality">Quality: Premium</p>
        <p class="brand">Brand: ShoesStore</p>
        <p class="description"><?php echo $product['description']; ?></p>

        <form method="post" action="cart.php">
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
            <label>Quantity:</label>
            <input type="number" name="quantity" value="1" min="1">
            <button type="submit" name="add_to_cart">Add to Cart</button>
        </form>
    </div>
</div>

<!-- Slider JS -->
<script>
let slideIndex = 0;
const slides = document.querySelectorAll('.slide');
const prev = document.querySelector('.prev');
const next = document.querySelector('.next');

function showSlide(index){
    slides.forEach((slide, i) => {
        slide.style.display = (i === index) ? 'block' : 'none';
    });
}

showSlide(slideIndex);

prev.addEventListener('click', () => {
    slideIndex = (slideIndex === 0) ? slides.length - 1 : slideIndex - 1;
    showSlide(slideIndex);
});

next.addEventListener('click', () => {
    slideIndex = (slideIndex === slides.length - 1) ? 0 : slideIndex + 1;
    showSlide(slideIndex);
});
</script>

<?php include '../inc/footer.php'; ?>
