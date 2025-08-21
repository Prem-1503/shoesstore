<?php
require_once '../inc/db.php';
include '../inc/header.php';

$message = '';
if(isset($_POST['register'])){
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email=?");
    $stmt->execute([$email]);

    if($stmt->rowCount() == 0){
        $stmt = $pdo->prepare("INSERT INTO users (name,email,password,phone,address) VALUES (?,?,?,?,?)");
        if($stmt->execute([$name,$email,$password,$phone,$address])){
            $message = "<span class='success-msg'>Registration successful! <a href='login.php'>Login here</a></span>";
        }
    } else {
        $message = "<span class='error-msg'>Email already exists!</span>";
    }
}
?>

<div class="login-container">
    <h1>Register</h1>
    <?php if($message != ''): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="post">
        <input type="text" name="name" placeholder="Full Name" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <input type="text" name="phone" placeholder="Phone"><br>
        <textarea name="address" placeholder="Address"></textarea><br>
        <button type="submit" name="register">Register</button>
    </form>
    <p style="text-align:center; margin-top:10px;">
        Already have an account? <a href="login.php">Login here</a>
    </p>
</div>

<?php include '../inc/footer.php'; ?>
