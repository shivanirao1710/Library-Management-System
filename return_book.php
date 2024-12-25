<?php
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = $_POST['book_id'];
    $member_id = $_POST['member_id'];

    // Return the book
    $conn->query("UPDATE books SET available_quantity = available_quantity + 1 WHERE book_id = $book_id");
    $conn->query("DELETE FROM transactions WHERE book_id = $book_id AND member_id = $member_id");

    echo "Book returned successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Book</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <h1>Return a Book</h1>
    </header>

    <form action="return_book.php" method="POST">
        <label for="book_id">Book ID:</label>
        <input type="number" id="book_id" name="book_id" required>

        <label for="member_id">Member ID:</label>
        <input type="number" id="member_id" name="member_id" required>

        <button type="submit">Return Book</button>
    </form>
    <a href="dashboard.php"><button>Back to Home</button></a>
</body>
</html>
