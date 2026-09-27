<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM admins WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows == 1) {
        $admin = $result->fetch_assoc();
        // Verify the password (using password_verify if you hashed it)
       if ($password === $admin['password']) { // Direct comparison for plain text passwords
    $_SESSION['admin_id'] = $admin['admin_id'];
    $_SESSION['admin_username'] = $admin['username'];
    header("Location: admin_dashboard.php");
    exit();
} else {
    $error_message = "Incorrect password.";
}
    } else {
        $error_message = "Incorrect username.";
    }
}
// ... (Handle error messages and redirect back to login if needed)
?>