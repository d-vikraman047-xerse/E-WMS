<?php
include 'db_connect.php'; // Database connection

$errors = []; // Array to store error messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs (very important!)
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);


    // Validation
    if (empty($username)) {
        $errors[] = "Username is required.";
    }
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    }
    if ($password != $confirm_password) {
        $errors[] = "Passwords do not match.";
    }
    if (empty($address)) {
        $errors[] = "Address is required.";
    }
    if (empty($phone)) {
        $errors[] = "Phone number is required.";
    }

    if (empty($errors)) { // If no errors
        $hashed_password = $password;

        $sql = "INSERT INTO users (username, password, email, address, phone) VALUES ('$username', '$hashed_password', '$email', '$address', '$phone')";

        if ($conn->query($sql) === TRUE) {
            // Redirect to a success page or login
            header("Location: register_success.php"); // Create register_success.php
            exit();
        } else {
            $errors[] = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <style>
      body {
            background-image: url("background.jpg"); /* Replace with your image */
            background-size: cover;
            font-family: sans-serif;
            color: white; /* Example text color for contrast */
        }
    </style>
</head>
<body>
    <h1>Register</h1>

    <?php if (!empty($errors)): ?>
        <div style="color: red;">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post">
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" required><br><br>

        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <label for="confirm_password">Confirm Password:</label><br>
        <input type="password" id="confirm_password" name="confirm_password" required><br><br>

        <label for="address">Address:</label><br>
        <textarea id="address" name="address" required></textarea><br><br>

        <label for="phone">Phone Number:</label><br>
        <input type="tel" id="phone" name="phone" required><br><br>

        <input type="submit" value="Register">
    </form>
</body>
</html>