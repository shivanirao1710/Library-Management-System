<?php
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $genre = $_POST['genre'];
    $publication_year = $_POST['publication_year'];
    $total_quantity = $_POST['total_quantity'];

    // Validate that total_quantity is a positive number
    if (!is_numeric($total_quantity) || $total_quantity <= 0) {
        $_SESSION['error'] = "Total quantity must be a number greater than 0.";
        header("Location: add_book.php");
        exit();
    }

    $available_quantity = $total_quantity;

    // Insert book into the database
    $stmt = $conn->prepare("INSERT INTO books (title, author, genre, publication_year, total_quantity, available_quantity) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiii", $title, $author, $genre, $publication_year, $total_quantity, $available_quantity);
    $stmt->execute();

    // Redirect to the dashboard after successful insertion
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Book</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <h1>Add New Book</h1>
    </header>

    <!-- Display the error message if the validation failed -->
    <?php
    if (isset($_SESSION['error'])) {
        echo '<p style="color: red;">' . $_SESSION['error'] . '</p>';
        unset($_SESSION['error']);
    }
    ?>

    <form action="add_book.php" method="POST">
        <label for="title">Book Title:</label>
        <input type="text" id="title" name="title" required>

        <label for="author">Author:</label>
        <input type="text" id="author" name="author" required>

        <label for="genre">Genre:</label>
        <input type="text" id="genre" name="genre" required>

        <label for="publication_year">Publication Year:</label>
        <input type="number" id="publication_year" name="publication_year" required>

        <label for="total_quantity">Total Quantity:</label>
        <input type="number" id="total_quantity" name="total_quantity" required>

        <button type="submit">Add Book</button>
    </form>
    <a href="dashboard.php"><button>Back to Home</button></a>
</body>
</html>
