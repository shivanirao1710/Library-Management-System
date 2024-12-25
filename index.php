<?php
session_start();
include('config.php');  // Include the database connection

// Check if the user is already logged in
if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit();
}

// Initialize error message
$error = '';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the username and password from the form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query the database to find the user
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    // Check if the user exists
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Check if the password matches
        if ($user['password'] == $password) {
            // Store the entire user record in the session (not just the username)
            $_SESSION['user'] = $user;  // Store the full user record
            $_SESSION['role'] = $user['role'];  // Optionally store the role separately

            // Redirect to the dashboard page
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid password. Please try again.";  // Incorrect password
        }
    } else {
        $error = "No such user found. Please try again.";  // User does not exist
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <h1>Library Management System</h1>
    </header>

    <main>
        <h2>Login</h2>
        
        <?php if ($error): ?>
            <p style="color: red;"><?php echo $error; ?></p>  <!-- Display error if any -->
        <?php endif; ?>
        
        <!-- Login form -->
        <form action="index.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required><br><br>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br><br>
            
            <button type="submit">Login</button>
        </form>
        
        <p><a href="register.php">Don't have an account? Register here.</a></p>
    </main>
</body>
</html>
