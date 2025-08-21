<?php
require_once '../inc/db.php';
session_start();

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$user_address = $_SESSION['user']['address'] ?? '';

if(empty($_SESSION['cart'])){
    echo "<p style='text-align:center;margin-top:50px;'>Your cart is empty!</p>";
    exit;
}

$ids = implode(',', array_keys($_SESSION['cart']));
$stmt = $pdo->query("SELECT * FROM products WHERE id IN ($ids)");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
foreach($products as $p){
    $qty = $_SESSION['cart'][$p['id']];
    $total += $p['price'] * $qty;
}

$message = '';
if(isset($_POST['checkout'])){
    $address = trim($_POST['address']);

    $stmt = $pdo->prepare("INSERT INTO orders (user_id,total_price,address) VALUES (?,?,?)");
    $stmt->execute([$user_id,$total,$address]);
    $order_id = $pdo->lastInsertId();

    foreach($products as $p){
        $qty = $_SESSION['cart'][$p['id']];
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id,product_id,quantity,price) VALUES (?,?,?,?)");
        $stmt->execute([$order_id,$p['id'],$qty,$p['price']]);
    }

    unset($_SESSION['cart']);
    $message = "✅ Order placed successfully!";
}

include '../inc/header.php';
?>

<h1 class="page-title">Checkout</h1>

<?php if($message != ''): ?>
    <p class="success-msg"><?php echo $message; ?></p>
<?php else: ?>
<div class="checkout-container">
    <div class="checkout-left">
        <table class="checkout-table">
            <tr>
                <th>Image</th>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
            </tr>
            <?php foreach($products as $p): 
                $qty = $_SESSION['cart'][$p['id']];
                $subtotal = $p['price'] * $qty;

                // Fetch first image from product_images
                $stmt_img = $pdo->prepare("SELECT image FROM product_images WHERE product_id=? LIMIT 1");
                $stmt_img->execute([$p['id']]);
                $img = $stmt_img->fetchColumn();
                $img = $img ? "../uploads/".$img : "../uploads/default.png";
            ?>
            <tr class="checkout-row">
                <td><img src="<?php echo $img; ?>" alt="<?php echo $p['name']; ?>" class="product-img"></td>
                <td><?php echo $p['name']; ?></td>
                <td>₹<?php echo $p['price']; ?></td>
                <td><?php echo $qty; ?></td>
                <td>₹<?php echo $subtotal; ?></td>
            </tr>
            <?php endforeach; ?>
            <tr class="total-row">
                <td colspan="4" style="text-align:right;"><strong>Total:</strong></td>
                <td><strong>₹<?php echo $total; ?></strong></td>
            </tr>
        </table>
    </div>

    <div class="checkout-right">
        <form method="post" class="checkout-form">
            <label for="address">Delivery Address:</label><br>
            <textarea name="address" id="address" required><?php echo htmlspecialchars($user_address); ?></textarea><br>
            <button type="submit" name="checkout">Place Order</button>
        </form>
    </div>
</div>
<?php endif; ?>

<style>
.page-title { text-align:center; margin:30px 0; font-size:28px; font-weight:bold; }
.checkout-container { display:flex; gap:30px; justify-content:center; flex-wrap:wrap; margin-bottom:50px; }
.checkout-left { flex:1 1 600px; }
.checkout-right { flex:1 1 300px; }
.checkout-table { width:100%; border-collapse:collapse; background:#fff; border-radius:10px; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,0.1); }
.checkout-table th, .checkout-table td { padding:12px; text-align:center; border-bottom:1px solid #ddd; }
.checkout-table th { background:#333; color:#fff; }
.checkout-row:hover { background:#f9f9f9; }
.product-img { width:80px; height:80px; object-fit:cover; border-radius:6px; }
.total-row td { font-size:18px; color:#ff6600; }
.checkout-form { background:#fff; padding:20px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.1); text-align:center; }
.checkout-form label { font-weight:bold; display:block; margin-bottom:10px; font-size:16px; }
.checkout-form textarea { width:100%; min-height:100px; padding:10px; border-radius:6px; border:1px solid #ccc; font-size:14px; margin-bottom:15px; resize:vertical; }
.checkout-form button { padding:12px 25px; background:#ff6600; color:#fff; font-size:16px; border:none; border-radius:6px; cursor:pointer; transition:background 0.3s, transform 0.2s; }
.checkout-form button:hover { background:#e65c00; transform:translateY(-2px); }
.success-msg { text-align:center; color:green; font-size:18px; margin-top:30px; }
@media(max-width:768px){ .checkout-container { flex-direction:column; align-items:center; } .product-img { width:60px; height:60px; } .checkout-form textarea { font-size:13px; } .checkout-form button { width:100%; font-size:14px; } }
</style>

<?php include '../inc/footer.php'; ?>
