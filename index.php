<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'cms_shop');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'];
    $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMS Shop</title>
</head>
<body>
    <h1>Welcome to CMS Shop</h1>
    
    <?php if (!isset($_SESSION['user_id'])): ?>
        <a href="register.php">Register</a> | <a href="login.php">Login</a>
    <?php else: ?>
        <a href="cart.php">My Cart</a>
        <?php if ($_SESSION['user_role'] == 'admin'): ?>
            | <a href="cms.php">Go to CMS</a>
        <?php endif; ?>
        | <a href="logout.php">Logout</a>
    <?php endif; ?>

    <h2>All Products</h2>
    <?php
    $result = $conn->query("SELECT id, name, description, price, image, created_at FROM products");
    while ($row = $result->fetch_assoc()): ?>
        <div>
            <h3><?php echo $row['name']; ?></h3>
            <p><?php echo $row['description']; ?></p>
            <p>Price: $<?php echo $row['price']; ?></p>
            <?php if ($row['image']): ?>
                <img src="images/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>" width="150">
            <?php endif; ?>
            <p>Added on: <?php echo $row['created_at']; ?></p>
            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] == 'user'): ?>
                <form method="post">
                    <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                    <button type="submit" name="add_to_cart">Add to Cart</button>
                </form>
            <?php endif; ?>
        </div>
        <hr>
    <?php endwhile; ?>
</body>
</html>