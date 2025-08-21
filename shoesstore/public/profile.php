<?php
require_once '../inc/db.php';
session_start();

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$message = '';

// Handle profile update
if(isset($_POST['update_profile'])){
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $password = $_POST['password'];

    if($password){ // Update password if provided
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET name=?, phone=?, address=?, password=? WHERE id=?");
        $stmt->execute([$name,$phone,$address,$hashed_password,$user_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET name=?, phone=?, address=? WHERE id=?");
        $stmt->execute([$name,$phone,$address,$user_id]);
    }

    // Update session data
    $_SESSION['user']['name'] = $name;
    $_SESSION['user']['phone'] = $phone;
    $_SESSION['user']['address'] = $address;

    $message = "<span class='success-msg'>Profile updated successfully!</span>";
}

// Fetch orders
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id=? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../inc/header.php';
?>

<div class="profile-container">
    <h1>My Profile</h1>
    <?php if($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="post">
        <label>Full Name:</label><br>
        <input type="text" name="name" value="<?php echo $_SESSION['user']['name']; ?>" required><br>

        <label>Email (cannot change):</label><br>
        <input type="email" value="<?php echo $_SESSION['user']['email']; ?>" disabled><br>

        <label>Phone:</label><br>
        <input type="text" name="phone" value="<?php echo $_SESSION['user']['phone'] ?? ''; ?>"><br>

        <label>Address:</label><br>
        <textarea name="address"><?php echo $_SESSION['user']['address'] ?? ''; ?></textarea><br>

        <label>Change Password:</label><br>
        <input type="password" name="password" placeholder="Leave blank to keep current password"><br>

        <button type="submit" name="update_profile">Update Profile</button>
    </form>

    <h2>My Orders</h2>
    <?php if($orders): ?>
    <table>
        <tr>
            <th>Order ID</th>
            <th>Total Price</th>
            <th>Status</th>
            <th>Date</th>
        </tr>
        <?php foreach($orders as $o): ?>
        <tr>
            <td><?php echo $o['id']; ?></td>
            <td>₹<?php echo $o['total_price']; ?></td>
            <td><?php echo $o['status']; ?></td>
            <td><?php echo $o['created_at']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php else: ?>
    <p>No orders yet.</p>
    <?php endif; ?>
</div>

<?php include '../inc/footer.php'; ?>
