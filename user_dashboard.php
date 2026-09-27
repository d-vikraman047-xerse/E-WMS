<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

// Initialize variables
$username = "User";
$user_id = "N/A";
$user = null;
$message = "";

// Fetch user data
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];
    $sql = "SELECT * FROM users WHERE user_id = '$user_id'";
    $result = $conn->query($sql);
    if ($result && $result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $username = $user['username'];
    } else {
        $message = "Error fetching user data.";
    }
}

// Handle waste item submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_waste'])) {
    // ... (All your form processing logic goes HERE)
    $item_name = mysqli_real_escape_string($conn, $_POST['item_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $quantity = intval($_POST['quantity']);
    $pickup_address = mysqli_real_escape_string($conn, $_POST['pickup_address']);

    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        $message = "File is not an image.";
        $uploadOk = 0;
    }

    if (file_exists($target_file)) {
        $message = "Sorry, file already exists.";
        $uploadOk = 0;
    }

    if ($_FILES["image"]["size"] > 5000000) { // 5MB
        $message = "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        $message .= " Your file was not uploaded.";
    } else {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = $target_file;

            $sql = "INSERT INTO waste_items (user_id, item_name, description, quantity, pickup_address, image_path) 
                    VALUES ('$user_id', '$item_name', '$description', '$quantity', '$pickup_address', '$image_path')";

            if ($conn->query($sql) === TRUE) {
                $message = "Waste item submitted successfully!";
            } else {
                $message = "Error submitting waste item: " . $conn->error;
            }
        } else {
            $message = "Sorry, there was an error uploading your file.";
        }
    }
} 
//Close the PHP if statement here. No HTML should be inside this block.
// Fetch user's submitted waste items (with pagination)
$items_per_page = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

$waste_items_sql = "SELECT * FROM waste_items WHERE user_id = '$user_id' LIMIT $offset, $items_per_page";
$waste_items_result = $conn->query($waste_items_sql);
$waste_items = [];
if ($waste_items_result && $waste_items_result->num_rows > 0) {
    while ($row = $waste_items_result->fetch_assoc()) {
        $waste_items[] = $row;
    }
}

$total_items_sql = "SELECT COUNT(*) AS total FROM waste_items WHERE user_id = '$user_id'";
$total_items_result = $conn->query($total_items_sql);
$total_items = 0;
if ($total_items_result && $total_items_result->num_rows == 1) {
    $row = $total_items_result->fetch_assoc();
    $total_items = $row['total'];
}
$total_pages = ceil($total_items / $items_per_page);
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <style>
        body {
            background-image: url("background.jpg");
            background-size: cover;
            font-family: sans-serif;
            color: white;
            padding: 20px;
        }
        .container {
            background-color: rgba(0, 0, 0, 0.7);
            padding: 20px;
            border-radius: 10px;
            max-width: 800px;
            margin: 0 auto;
        }
        .message {
            margin-top: 10px;
            padding: 10px;
            border-radius: 5px;
        }
        .success {
            background-color: rgba(0, 255, 0, 0.3);
            color: green;
        }
        .error {
            background-color: rgba(255, 0, 0, 0.3);
            color: red;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"],
        input[type="number"],
        input[type="file"],
        textarea {
            width: calc(100% - 12px);
            padding: 8px;
            margin-bottom: 10px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #f8f8f8;
            color: black;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .waste-item {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            background-color: rgba(255, 255, 255, 0.1);
            overflow: hidden;
        }
        .waste-item img {
            width: 200px;
            height: 200px;
            object-fit: cover;
            float: left;
            margin-right: 10px;
        }
        .waste-item .details {
            overflow: hidden;
        }
        .pagination {
            margin-top: 20px;
            text-align: center;
        }

        .pagination a {
            display: inline-block;
            padding: 8px 16px;
            margin: 0 4px;
            text-decoration: none;
            color: #fff;
            background-color: rgba(0, 0, 0, 0.5);
            border-radius: 4px;
        }

        .pagination a.active {
            background-color: #4CAF50;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo $username; ?>!</h1>

        <p>User ID: <?php echo $user_id; ?></p>
        <?php if ($user): ?>
            <p>Email: <?php echo $user['email']; ?></p>
            <p>Address: <?php echo $user['address']; ?></p>
            <p>Phone: <?php echo $user['phone']; ?></p>
        <?php endif; ?>

       <h2>Submit E-Waste Item</h2>
<?php if ($message): ?>
    <div class="message <?php echo strpos($message, 'Error') === 0 ? 'error' : 'success'; ?>">
        <?php echo $message; ?>
    </div>
<?php endif; ?>
<form method="post" enctype="multipart/form-data">
    <label for="item_name">Item Name:</label>
    <input type="text" id="item_name" name="item_name" required><br><br>

    <label for="description">Description:</label>
    <textarea id="description" name="description" required></textarea><br><br>

    <label for="quantity">Quantity:</label>
    <input type="number" id="quantity" name="quantity" value="1" min="1" required><br><br>

    <label for="pickup_address">Pickup Address:</label>
    <textarea id="pickup_address" name="pickup_address" required></textarea><br><br>

    <label for="image">Product Image:</label>
    <input type="file" name="image" id="image" required><br><br>

    <input type="submit" name="submit_waste" value="Submit Waste Item">
</form>        <h2>Your Submitted E-Waste Items</h2>
        <?php if (!empty($waste_items)): ?>
            <?php foreach ($waste_items as $item): ?>
                <div class="waste-item">
                    <?php if (!empty($item['image_path'])): ?>
                        <img src="<?php echo $item['image_path']; ?>" alt="Waste Item Image">
                    <?php endif; ?>
                    <div class="details">
                        <h3><?php echo htmlspecialchars($item['item_name']); ?></h3>
                        <p>Description: <?php echo htmlspecialchars($item['description']); ?></p>
                        <p>Quantity: <?php echo htmlspecialchars($item['quantity']); ?></p>
                        <p>Pickup Address: <?php echo htmlspecialchars($item['pickup_address']); ?></p>
                        <p>Status: <?php echo htmlspecialchars($item['status']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>">&laquo; Previous</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" <?php if ($i == $page) echo 'class="active"'; ?>><?php echo $i; ?></a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?>">Next &raquo;</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p>You haven't submitted any e-waste items yet.</p>
        <?php endif; ?>

        <br>
        <a href="logout.php">Logout</a>
    </div>
</body>
</html>