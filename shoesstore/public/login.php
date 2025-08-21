<?php
require_once '../inc/db.php';
include '../inc/header.php';

$message = '';

// Handle login form submission
if(isset($_POST['login'])){
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Fetch user by email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email=?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if($user && password_verify($password, $user['password'])){
        // Start session safely
        if(session_status() == PHP_SESSION_NONE){
            session_start();
        }

        // Set session and redirect based on role
        if($user['role'] == 'admin'){
            $_SESSION['admin'] = $user;
            header("Location: ../admin/index.php");
            exit;
        } else {
            $_SESSION['user'] = $user;
            header("Location: index.php");
            exit;
        }
    } else {
        $message = "Invalid Email or Password!";
    }
}
?>

<div class="login-container">
    <h1>Login</h1>
    <?php if($message != ''): ?>
        <p class="error-msg"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="post">
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit" name="login">Login</button>
    </form>
    <p style="text-align:center; margin-top:10px;">
        Don't have an account? <a href="register.php">Register here</a>
    </p>
</div>

<?php include '../inc/footer.php'; ?>
