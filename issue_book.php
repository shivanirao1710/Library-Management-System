<?php
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = $_POST['book_id'];
    $member_id = $_POST['member_id'];

    // Check if the book is available
    $stmt = $conn->prepare("SELECT * FROM books WHERE book_id = ? AND available_quantity > 0");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Issue the book
        $conn->query("INSERT INTO transactions (book_id, member_id, issue_date) VALUES ($book_id, $member_id, NOW())");
        $conn->query("UPDATE books SET available_quantity = available_quantity - 1 WHERE book_id = $book_id");

        echo "Book issued successfully!";
    } else {
        echo "Book is not available.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Issue Book</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <h1>Issue a Book</h1>
    </header>

    <form action="issue_book.php" method="POST">
        <label for="book_id">Book ID:</label>
        <input type="number" id="book_id" name="book_id" required>

        <label for="member_id">Member ID:</label>
        <input type="number" id="member_id" name="member_id" required>

        <button type="submit">Issue Book</button>
    </form>
    <a href="dashboard.php"><button>Back to Home</button></a>
</body>
</html>
