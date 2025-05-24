<?php
session_start();

// Sample dog products (in a real app, fetch from a database)
$dogs = [
    [
        "name" => "Golden Retriever",
        "img" => "https://images.unsplash.com/photo-1558788353-f76d92427f16?auto=format&fit=crop&w=400&q=80",
        "desc" => "Friendly and tolerant, great family dog.",
        "price" => "$500"
    ],
    [
        "name" => "Siberian Husky",
        "img" => "https://images.unsplash.com/photo-1518717758536-85ae29035b6d?auto=format&fit=crop&w=400&q=80",
        "desc" => "Energetic and free-spirited.",
        "price" => "$600"
    ],
    [
        "name" => "Beagle",
        "img" => "https://images.unsplash.com/photo-1507146426996-ef05306b995a?auto=format&fit=crop&w=400&q=80",
        "desc" => "Gentle and playful, perfect with kids.",
        "price" => "$400"
    ],
];

// Slideshow logic
$current = isset($_GET['dog']) ? intval($_GET['dog']) : 0;
if ($current < 0) $current = 0;
if ($current >= count($dogs)) $current = count($dogs) - 1;
$dog = $dogs[$current];

// Simulate authentication (in a real app, use actual user login)
$is_logged_in = isset($_SESSION['user']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dog Shop - PetShop Home</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f6f6f6;
            margin: 0;
            padding: 0;
        }
        header {
            background: #4CAF50;
            color: #fff;
            padding: 20px 0;
            text-align: center;
        }
        nav {
            background: #333;
            color: #fff;
            padding: 10px 0;
            text-align: center;
        }
        nav a {
            color: #fff;
            margin: 0 20px;
            text-decoration: none;
            font-weight: bold;
        }
        .container {
            max-width: 500px;
            margin: 40px auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px #ddd;
            padding: 30px;
        }
        .dog-view {
            text-align: center;
        }
        .dog-view img {
            width: 300px;
            height: 260px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid #eee;
        }
        .dog-view h2 {
            margin: 18px 0 8px;
        }
        .dog-view p {
            margin: 8px 0;
        }
        .dog-view .price {
            color: #4CAF50;
            font-size: 1.2em;
            font-weight: bold;
        }
        .slideshow-nav {
            margin: 18px 0;
        }
        .slideshow-nav a {
            background: #4CAF50;
            color: #fff;
            padding: 7px 18px;
            margin: 0 10px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.15s;
        }
        .slideshow-nav a.disabled {
            background: #aaa;
            pointer-events: none;
        }
        .add-cart-btn {
            background: #ff9800;
            color: #fff;
            padding: 12px 26px;
            font-size: 1em;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 16px;
            margin-bottom: 8px;
            font-weight: bold;
        }
        .alert {
            color: #c0392b;
            margin-top: 10px;
            font-weight: bold;
        }
        footer {
            background: #222;
            color: #fff;
            text-align: center;
            padding: 15px 0;
            margin-top: 50px;
        }
    </style>
</head>
<body>
<header>
    <h1>Happy Paws PetShop</h1>
    <p>Find your new best friend!</p>
</header>
<nav>
    <a href="#">Home</a>
    <a href="#">Shop</a>
    <a href="#">Adopt</a>
    <a href="#">Contact</a>
    <a href="#">About</a>
    <?php if ($is_logged_in): ?>
        <a href="logout.php" style="float:right">Logout</a>
    <?php else: ?>
        <a href="signin.php" style="float:right">Sign In</a>
        <a href="signup.php" style="float:right">Sign Up</a>
    <?php endif; ?>
</nav>
<div class="container">
    <div class="dog-view">
        <img src="<?php echo htmlspecialchars($dog['img']); ?>" alt="<?php echo htmlspecialchars($dog['name']); ?>">
        <h2><?php echo htmlspecialchars($dog['name']); ?></h2>
        <p><?php echo htmlspecialchars($dog['desc']); ?></p>
        <div class="price"><?php echo htmlspecialchars($dog['price']); ?></div>
        <form method="post" style="margin-top: 20px;">
            <button class="add-cart-btn" type="button" onclick="showLoginAlert()">Add to Cart</button>
        </form>
        <div class="alert" id="login-alert" style="display:none;">
            You must <a href="signin.php">sign in</a> or <a href="signup.php">sign up</a> to add to cart.
        </div>
        <div class="slideshow-nav">
            <a href="?dog=<?php echo $current-1; ?>" class="<?php echo ($current==0) ? 'disabled' : ''; ?>">Previous</a>
            <a href="?dog=<?php echo $current+1; ?>" class="<?php echo ($current==count($dogs)-1) ? 'disabled' : ''; ?>">Next</a>
        </div>
        <div>Dog <?php echo $current+1; ?> of <?php echo count($dogs); ?></div>
    </div>
</div>
<footer>
    &copy; <?php echo date("Y"); ?> Happy Paws PetShop. All rights reserved.
</footer>
<script>
function showLoginAlert() {
    document.getElementById('login-alert').style.display = 'block';
}
</script>
</body>
</html>