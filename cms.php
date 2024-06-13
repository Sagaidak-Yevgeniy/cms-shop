<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header('Location: index.php');
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'cms_shop');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_product'])) {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $image = $_FILES['image']['name'];
        $target = "images/" . basename($image);

        $stmt = $conn->prepare("INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssds", $name, $description, $price, $image);
        $stmt->execute();
        $stmt->close();

        move_uploaded_file($_FILES['image']['tmp_name'], $target);
        header('Location: cms.php');
    } elseif (isset($_POST['edit_product'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $image = $_FILES['image']['name'];
        $target = "images/" . basename($image);

        if ($image) {
            $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, image = ? WHERE id = ?");
            $stmt->bind_param("ssdsi", $name, $description, $price, $image, $id);
            move_uploaded_file($_FILES['image']['tmp_name'], $target);
        } else {
            $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ? WHERE id = ?");
            $stmt->bind_param("ssdi", $name, $description, $price, $id);
        }
        $stmt->execute();
        $stmt->close();
        header('Location: cms.php');
    } elseif (isset($_POST['delete_product'])) {
        $id = $_POST['id'];
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        header('Location: cms.php');
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMS Panel</title>
</head>
<body>
    <h1>CMS Panel</h1>
    <a href="index.php">Back to Home</a> | <a href="logout.php">Logout</a>

    <h2>Add New Product</h2>
    <form method="post" enctype="multipart/form-data">
        Product Name: <input type="text" name="name" required><br>
        Description: <textarea name="description" required></textarea><br>
        Price: <input type="text" name="price" required><br>
        Image: <input type="file" name="image"><br>
        <button type="submit" name="add_product">Add Product</button>
    </form>

    <h2>All Products</h2>
    <?php
    $result = $conn->query("SELECT id, name, description, price, image FROM products");
    while ($row = $result->fetch_assoc()): ?>
        <div>
            <h3><?php echo $row['name']; ?></h3>
            <p><?php echo $row['description']; ?></p>
            <p>Price: $<?php echo $row['price']; ?></p>
            <?php if ($row['image']): ?>
                <img src="images/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>" width="150">
            <?php endif; ?>
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                Product Name: <input type="text" name="name" value="<?php echo $row['name']; ?>" required><br>
                Description: <textarea name="description" required><?php echo $row['description']; ?></textarea><br>
                Price: <input type="text" name="price" value="<?php echo $row['price']; ?>" required><br>
                Image: <input type="file" name="image"><br>
                <button type="submit" name="edit_product">Edit Product</button>
                <button type="submit" name="delete_product">Delete Product</button>
            </form>
        </div>
        <hr>
    <?php endwhile; ?>
</body>
</html>