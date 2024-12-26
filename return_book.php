<?php
// Include the database connection
include('config.php');

// Initialize the variable for member_id
$member_id = null;

// Handle form submission for member_id
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['member_id'])) {
    $member_id = $_POST['member_id']; // Get the member_id from input
}

// Fetch the list of books issued to the member (only those that are not returned yet)
if ($member_id) {
    $sql = "SELECT t.id, b.title, t.issue_date 
            FROM transactions t 
            JOIN books b ON t.book_id = b.id 
            WHERE t.member_id = ? AND t.return_date IS NULL";  // Only books that are not yet returned

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $member_id); // Bind the member_id parameter to the query
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = null; // If no member_id, return no results
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Book</title>
    <link rel="stylesheet" href="assets/css/style.css">  <!-- Your CSS link -->
</head>
<body>
    <header>
        <h1>Return a Book</h1>
    </header>

    <!-- Form to enter member_id and return book -->
    <form action="return_book.php" method="POST">
        <label for="member_id">Member ID:</label>
        <input type="number" id="member_id" name="member_id" required>

        <button type="submit">Show Issued Books</button>
    </form>

    <?php if ($member_id): ?>
        <!-- If the member_id is entered and books are found, show the list of books issued -->
        <form action="return_book.php" method="POST">
            <label for="transaction_id">Select Transaction to Return:</label>
            <select name="transaction_id" id="transaction_id" required>
                <?php
                // Check if the member has any issued books
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['id'] . "'>Transaction ID: " . $row['id'] . " - " . $row['title'] . " (Issued on: " . $row['issue_date'] . ")</option>";
                    }
                } else {
                    echo "<option value=''>No books issued to this member.</option>";
                }
                ?>
            </select>

            <button type="submit">Return Book</button>
        </form>
    <?php endif; ?>

    <a href="dashboard.php"><button>Back to Home</button></a>
</body>
</html>

<?php
// Handle book return logic when the transaction_id is provided
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['transaction_id'])) {
    // Get the transaction_id from the form submission
    $transaction_id = $_POST['transaction_id'];

    // Validate input
    if (isset($transaction_id)) {
        // Start a database transaction to ensure consistency
        $conn->begin_transaction();

        try {
            // 1. Get the book_id associated with the transaction
            $stmt = $conn->prepare("SELECT book_id FROM transactions WHERE id = ?");
            $stmt->bind_param("i", $transaction_id);  // Bind transaction_id
            $stmt->execute();
            $result = $stmt->get_result();
            $book = $result->fetch_assoc();

            if ($book) {
                // 2. Update the available quantity of the book (increment by 1)
                $book_id = $book['book_id'];
                $stmt_update = $conn->prepare("UPDATE books SET available_quantity = available_quantity + 1 WHERE id = ?");
                $stmt_update->bind_param("i", $book_id);  // Bind book_id
                $stmt_update->execute();
            }

            // Commit the transaction (both updates should be successful)
            $conn->commit();

            echo "Book returned successfully! Available quantity updated.";

        } catch (Exception $e) {
            // If any error occurs, roll back the transaction
            $conn->rollback();
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Invalid transaction data.";
    }
}
?>
