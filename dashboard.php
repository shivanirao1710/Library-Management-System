<?php
session_start();
include('config.php');  // Include the database connection

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

// Access the user information from the session (user is now an associative array)
$user = $_SESSION['user'];  // The session now contains the entire user record
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <!-- Display the username from the session -->
        <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h1>
        <a href="logout.php"><button>Logout</button></a>
    </header>

    <nav>
        <a href="add_book.php"><button>Add Book</button></a>
        <a href="issue_book.php"><button>Issue Book</button></a>
        <a href="return_book.php"><button>Return Book</button></a>
    </nav>

    <section>
        <h2>Library Books</h2>
        
        <!-- Book Table -->
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Available Quantity</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch and display books from the database
                $books_result = $conn->query("SELECT * FROM books");
                while ($book = $books_result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($book['title']) . "</td>";
                    echo "<td>" . htmlspecialchars($book['author']) . "</td>";
                    echo "<td>" . htmlspecialchars($book['available_quantity']) . "</td>";
                    echo "<td><a href='delete_book.php?book_id=" . $book['id'] . "'><button>Delete</button></a></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </section>
</body>
</html>
