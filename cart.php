<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'user') {
    header('Location: index.php');
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'cms_shop');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_from_cart'])) {
    $cart_id = $_POST['cart_id'];
    $stmt = $conn->prepare("DELETE FROM cart WHERE id = ?");
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart</title>
</head>
<body>
    <h1>My Cart</h1>
    <a href="index.php">Back to Home</a> | <a href="logout.php">Logout</a>

    <h2>Products in Cart</h2>
    <?php
    $user_id = $_SESSION['user_id'];
    $result = $conn->query("SELECT cart.id as cart_id, products.name, products.description, products.price, products.image
                            FROM cart
                            JOIN products ON cart.product_id = products.id
                            WHERE cart.user_id = $user_id");
    while ($row = $result->fetch_assoc()): ?>
        <div>
            <h3><?php echo $row['name']; ?></h3>
            <p><?php echo $row['description']; ?></p>
            <p>Price: $<?php echo $row['price']; ?></p>
            <?php if ($row['image']): ?>
                <img src="images/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>" width="150">
            <?php endif; ?>
            <form method="post">
                <input type="hidden" name="cart_id" value="<?php echo $row['cart_id']; ?>">
                <button type="submit" name="remove_from_cart">Remove from Cart</button>
            </form>
        </div>
        <hr>
    <?php endwhile; ?>
</body>
</html>