<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <style>
        body {
            background-image: url("admin_background.jpg"); /* Replace with your image path */
            background-size: cover;
            background-repeat: no-repeat;
            font-family: sans-serif;
            display: flex; /* Center the login form */
            justify-content: center;
            align-items: center;
            min-height: 100vh; /* Ensure full viewport height */
            margin: 0; /* Remove default body margin */
            color: white; /* Set default text color to white for better contrast */
        }

        .login-container {
            background-color: rgba(0, 0, 0, 0.7); /* Slightly transparent black background */
            padding: 20px;
            border-radius: 10px;
            width: 300px; /* Adjust as needed */
            text-align: center; /* Center the form content */
        }
        label {
            display: block;
            margin-bottom: 5px;
            text-align: left; /* Align labels to the left */
        }

        input[type="text"],
        input[type="password"] {
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
            width: 100%; /* Make submit button full width */
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }
        h1{
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Admin Login</h1>
        <form method="post" action="admin_login_process.php">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required><br><br>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required><br><br>

            <input type="submit" value="Login">
        </form>
    </div>
</body>
</html>