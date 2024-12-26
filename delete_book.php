<?php
session_start();
include('config.php');  // Include the database connection

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Check if the book_id is set in the URL
if (isset($_GET['book_id'])) {
    $book_id = $_GET['book_id'];

    // Start a transaction to ensure data consistency
    $conn->begin_transaction();

    try {
        // 1. Delete from `book_categories` (many-to-many relationship table)
        $stmt = $conn->prepare("DELETE FROM book_categories WHERE book_id = ?");
        $stmt->bind_param("i", $book_id);
        $stmt->execute();

        // 2. Delete from `transactions` (remove all transactions for this book)
        $stmt = $conn->prepare("DELETE FROM transactions WHERE book_id = ?");
        $stmt->bind_param("i", $book_id);
        $stmt->execute();

        // 3. Delete from `books` (remove the book from the books table)
        $stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
        $stmt->bind_param("i", $book_id);
        $stmt->execute();

        // Commit the transaction
        $conn->commit();

        // Redirect back to the dashboard
        header("Location: dashboard.php");
        exit();

    } catch (Exception $e) {
        // Rollback the transaction if any error occurs
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
} else {
    // If no book_id is provided, redirect to the dashboard
    header("Location: dashboard.php");
    exit();
}
?>
