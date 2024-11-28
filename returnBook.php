<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'eLibrary';

$mysqli = new mysqli($host, $user, $password, $database);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

ini_set('display_errors', 1);
error_reporting(E_ALL);

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $registrationNo = $mysqli->real_escape_string($_POST['registrationNo']);
    $isbn = $mysqli->real_escape_string($_POST['isbn']);

    // Fetch student_id using registration_number
    $studentQuery = "SELECT id FROM students WHERE registration_number = '$registrationNo'";
    $studentResult = $mysqli->query($studentQuery);

    if ($studentResult->num_rows == 0) {
        $message = 'Student not registered.';
    } else {
        $studentData = $studentResult->fetch_assoc();
        $student_id = $studentData['id'];

        // Fetch book_id using isbn
        $bookQuery = "SELECT id FROM books WHERE isbn = '$isbn'";
        $bookResult = $mysqli->query($bookQuery);

        if ($bookResult->num_rows == 0) {
            $message = 'Book not found.';
        } else {
            $bookData = $bookResult->fetch_assoc();
            $book_id = $bookData['id'];

            // Check if the book is borrowed by the student
            $borrowedQuery = "SELECT id, return_date FROM borrowed_books WHERE student_id = $student_id AND book_id = $book_id AND return_date IS NULL";
            $borrowedResult = $mysqli->query($borrowedQuery);

            if ($borrowedResult->num_rows == 0) {
                $message = 'No record of this book being borrowed by the student.';
            } else {
                $borrowedData = $borrowedResult->fetch_assoc();
                $borrowed_id = $borrowedData['id'];

                // Update return_date in borrowed_books
                $returnQuery = "UPDATE borrowed_books SET return_date = CURDATE() WHERE id = $borrowed_id";

                if ($mysqli->query($returnQuery)) {
                    // Increment the book quantity
                    $updateBookQuery = "UPDATE books SET quantity = quantity + 1 WHERE id = $book_id";
                    if ($mysqli->query($updateBookQuery)) {
                        $message = 'Book returned successfully.';
                    } else {
                        $message = 'Error updating book quantity: ' . $mysqli->error;
                    }
                } else {
                    $message = 'Error updating return record: ' . $mysqli->error;
                }
            }
        }
    }
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Book</title>
    <link rel="stylesheet" href="../assets/css/returnBook.css">


</head>
<body>

    <div class="navbar">
        <a href="./adminDashboard.php" class="navbar-brand">Library Admin</a>
        <ul class="nav-links">
            <li><a href="adminDashboard.php" class="nav-link">Admin</a></li>
            <li><a href="../studentLogin.php" class="nav-link">Student</a></li>
            <li><a href="../showBook.php" class="nav-link">Show Books</a></li>
            <li><a href="#" class="nav-link">About Us</a></li>
            <li><a href="#" class="nav-link">Contact</a></li>
        </ul>
    </div>

    <h1>Return a Book <a href='./adminDashboard.php' class="back-btn">Back</a></h1>

    <div class="form-container">
        <form action="returnBook.php" method="POST">
            <label for="registrationNo">Registration No:</label>
            <input type="text" id="registrationNo" name="registrationNo" required>

            <label for="isbn">ISBN:</label>
            <input type="text" id="isbn" name="isbn" required>

            <button type="submit">Return Book</button>
        </form>

        <?php if ($message) : ?>
            <p id="message"><?php echo $message; ?></p>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2024 Library Management System | Developer Yuvraj Sinha</p>
    </footer>

</body>
</html>
