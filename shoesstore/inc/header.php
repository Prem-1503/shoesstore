<?php
// Start session safely
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Initialize cart session if not set
if(!isset($_SESSION['cart'])){
    $_SESSION['cart'] = [];
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Shoes Store</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        /* ======= BASIC HEADER ======= */
        body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f4f4; }
        header { 
            background: #222; 
            color: #fff; 
            padding: 15px 30px; 
            display: flex; 
            align-items: center; 
            justify-content: space-between; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .logo a { 
            color: #ffcc00; 
            text-decoration: none; 
            font-size: 28px; 
            font-weight: bold; 
            letter-spacing: 1px;
            transition: 0.3s;
        }
        .logo a:hover {
            color: #fff;
            text-shadow: 0 0 10px #ffcc00;
        }

        nav ul { 
            list-style: none; 
            margin: 0; 
            padding: 0; 
            display: flex; 
            gap: 20px; 
        }
        nav ul li { position: relative; }

        nav ul li a { 
            color: #fff; 
            text-decoration: none; 
            padding: 5px 0; 
            font-weight: 500; 
            position: relative; 
            transition: 0.3s; 
        }

        /* Hover underline animation */
        nav ul li a::after {
            content: '';
            position: absolute;
            width: 0%;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: #ffcc00;
            transition: 0.3s;
        }

        nav ul li a:hover::after {
            width: 100%;
        }

        /* Dropdown highlight effect */
        nav ul li a:hover {
            color: #ffcc00;
        }

        /* Responsive */
        @media(max-width:768px){
            header { flex-direction: column; align-items: flex-start; padding: 15px; }
            nav ul { flex-direction: column; gap: 10px; margin-top: 10px; width: 100%; }
            nav ul li a { width: 100%; display: block; }
        }
    </style>
</head>
<body>
<header>
    <div class="logo">
        <a href="index.php">Shoe Buzz</a>
    </div>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="cart.php">Cart (<?php echo count($_SESSION['cart']); ?>)</a></li>

            <?php if(isset($_SESSION['admin'])): ?>
                <li><a href="../admin/index.php">Admin Panel</a></li>
                <li><a href="../admin/logout.php">Logout</a></li>
            <?php elseif(isset($_SESSION['user'])): ?>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
<main>
