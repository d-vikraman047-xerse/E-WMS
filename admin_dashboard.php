<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

include 'db_connect.php';

// Initialize $message
$message = "";

// Fetch ALL submitted waste items (no user_id filter)
$waste_items_sql = "SELECT w.*, u.username FROM waste_items w LEFT JOIN users u ON w.user_id = u.user_id ORDER BY w.submission_date DESC";
$waste_items_result = $conn->query($waste_items_sql);
if (!$waste_items_result) {
    die("SQL Error: " . $conn->error);
}

$waste_items = [];
if ($waste_items_result->num_rows > 0) {
    while ($row = $waste_items_result->fetch_assoc()) {
        $waste_items[] = $row;
    }
}

// Handle status updates
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $item_id = mysqli_real_escape_string($conn, $_POST['item_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);

    $update_sql = "UPDATE waste_items SET status = '$new_status' WHERE item_id = '$item_id'";
    if ($conn->query($update_sql) === TRUE) {
        $message = "Status updated successfully!";
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $message = "Error updating status: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .waste-item-image { /* Style for the images */
            width: 200px;
            height: 200px;
            object-fit: cover;
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
        select, button{
            padding:5px;
        }
    </style>
</head>
<body>
    <h1>Admin Dashboard</h1>

    <?php if ($message): ?>
        <div class="message <?php echo strpos($message, 'Error') === 0 ? 'error' : 'success'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <h2>Submitted Waste Items</h2>

    <?php if (!empty($waste_items)): ?>
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Item Name</th>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Pickup Address</th>
                    <th>Submitted By</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($waste_items as $item): ?>
                    <tr>
                        <td>
                            <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="Waste Item Image" class="waste-item-image">
                        </td>
                        <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                        <td><?php echo htmlspecialchars($item['description']); ?></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($item['pickup_address']); ?></td>
                        <td><?php echo htmlspecialchars(isset($item['username']) ? $item['username'] : 'User Deleted'); ?></td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                                <select name="status">
                                    <option value="Pending" <?php if ($item['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                    <option value="Collected" <?php if ($item['status'] == 'Collected') echo 'selected'; ?>>Collected</option>
                                    <option value="Rejected" <?php if ($item['status'] == 'Rejected') echo 'selected'; ?>>Rejected</option>
                                </select>
                                <button type="submit" name="update_status">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No waste items have been submitted yet.</p>
    <?php endif; ?>

    <br>
    <a href="admin_logout.php">Logout</a>

</body>
</html>