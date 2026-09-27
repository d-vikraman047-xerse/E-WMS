<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = $_POST['product_name'];
    $product_type = $_POST['product_type'];
    $condition = $_POST['condition'];
    $description = $_POST['description'];
    $pickup_address = $_POST['pickup_address'];

    $sql = "INSERT INTO products (user_id, product_name, product_type, condition, description, pickup_address) 
            VALUES ('$user_id', '$product_name', '$product_type', '$condition', '$description', '$pickup_address')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Product listed successfully for pickup!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
</head>
<body>
    <h2>Welcome, <?php echo $_SESSION['username']; ?></h2>
    <h3>List Electronic Waste</h3>
    <form method="POST">
        <label>Product Name:</label>
        <input type="text" name="product_name" required><br>
        <label>Product Type:</label>
        <input type="text" name="product_type"><br>
        <label>Condition:</label>
        <input type="text" name="condition"><br>
        <label>Description:</label>
        <textarea name="description"></textarea><br>
        <label>Pickup Address:</label>
        <textarea name="pickup_address" required></textarea><br>
        <button type="submit">Submit</button>
    </form>
</body>
</html>
