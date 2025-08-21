<?php
require_once '../inc/db.php';
session_start();

// Add to cart
if(isset($_POST['add_to_cart'])){
    $product_id = $_POST['product_id'];
    $quantity = (int)$_POST['quantity'];

    if(isset($_SESSION['cart'][$product_id])){
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
    header("Location: cart.php");
    exit;
}

// Update quantities
if(isset($_POST['update_cart'])){
    foreach($_POST['quantities'] as $id => $qty){
        if($qty <= 0){
            unset($_SESSION['cart'][$id]);
        } else {
            $_SESSION['cart'][$id] = $qty;
        }
    }
    header("Location: cart.php");
    exit;
}

// Remove single item
if(isset($_GET['remove'])){
    $id = (int)$_GET['remove'];
    unset($_SESSION['cart'][$id]);
    header("Location: cart.php");
    exit;
}

include '../inc/header.php';

$cart_items = [];
$total = 0;
if(!empty($_SESSION['cart'])){
    $ids = implode(',', array_keys($_SESSION['cart']));
    $stmt = $pdo->query("SELECT * FROM products WHERE id IN ($ids)");
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<h1>Your Cart</h1>

<?php if(!empty($cart_items)): ?>
<form method="post">
<table class="cart-table">
    <tr>
        <th>Image</th>
        <th>Product</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Subtotal</th>
        <th>Action</th>
    </tr>
    <?php foreach($cart_items as $item): 
        $qty = $_SESSION['cart'][$item['id']];
        $subtotal = $item['price'] * $qty;
        $total += $subtotal;

        // Get first image of product
        $stmt_img = $pdo->prepare("SELECT image FROM product_images WHERE product_id=? LIMIT 1");
        $stmt_img->execute([$item['id']]);
        $img = $stmt_img->fetchColumn() ?: 'default.png';
    ?>
    <tr>
        <td><img src="../uploads/<?php echo $img; ?>" class="cart-img"></td>
        <td><?php echo $item['name']; ?></td>
        <td>₹<?php echo $item['price']; ?></td>
        <td><input type="number" name="quantities[<?php echo $item['id']; ?>]" value="<?php echo $qty; ?>" min="0"></td>
        <td>₹<?php echo $subtotal; ?></td>
        <td><a href="?remove=<?php echo $item['id']; ?>" class="remove-btn">Remove</a></td>
    </tr>
    <?php endforeach; ?>
</table>
<p class="cart-total"><strong>Total: ₹<?php echo $total; ?></strong></p>
<button type="submit" name="update_cart" class="update-btn">Update Cart</button>
<a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
</form>
<?php else: ?>
<p>Your cart is empty.</p>
<?php endif; ?>

<?php include '../inc/footer.php'; ?>
