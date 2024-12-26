<?php
include('config.php');  // Include your DB connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = $_POST['book_id'];
    $member_id = $_POST['member_id'];

    // Check if the book is available
    $stmt = $conn->prepare("SELECT * FROM books WHERE id = ? AND available_quantity > 0");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Issue the book
        $stmt_issue = $conn->prepare("INSERT INTO transactions (book_id, member_id, issue_date) VALUES (?, ?, NOW())");
        $stmt_issue->bind_param("ii", $book_id, $member_id);
        $stmt_issue->execute();

        // Update the available quantity
        $stmt_update = $conn->prepare("UPDATE books SET available_quantity = available_quantity - 1 WHERE id = ?");
        $stmt_update->bind_param("i", $book_id);
        $stmt_update->execute();

        echo "Book issued successfully!";
    } else {
        echo "Book is not available.";
    }

    $stmt->close();
    $stmt_issue->close();
    $stmt_update->close();
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
