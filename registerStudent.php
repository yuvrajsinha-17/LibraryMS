<?php

include '../db.php'; 


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $firstName = $_POST['fname'];
    $lastName = $_POST['lname'];
    $registrationNumber = $_POST['registrationNo'];
    $password = $_POST['password'];

 
    if (empty($firstName) || empty($lastName) || empty($registrationNumber) || empty($password)) {
        echo "Please fill all fields!";
        exit();
    }

    
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);


    $query = "INSERT INTO students (first_name, last_name, registration_number, password) 
              VALUES (?, ?, ?, ?)";
    
    
    if ($mysqli) {
        if ($stmt = $mysqli->prepare($query)) {
            $stmt->bind_param("ssss", $firstName, $lastName, $registrationNumber, $hashedPassword);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                
                header("Location: registrationSuccess.php");
                exit();
            } else {
                echo "Error: Could not register the student.";
            }

            $stmt->close();
        } else {
            echo "Error: " . $mysqli->error;
        }
    } else {
        echo "Database connection failed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Student</title>
    <link rel="stylesheet" href='../assets/css/registerStudent.css'>

</head>
<body>

    <div class="navbar">
        <a href="./adminDashboard.php" class="navbar-brand">Library Admin</a>
        <ul class="nav-links">
            <li><a href="adminLogin.php" class="nav-link">Admin</a></li>
            <li><a href="index.php" class="nav-link">Student</a></li>
            <li><a href="showBook.php" class="nav-link">Show Books</a></li>
            <li><a href="#" class="nav-link">About Us</a></li>
            <li><a href="#" class="nav-link">Contact</a></li>
        </ul>
    </div>


    <h1>Student Registration</h1>
    
    <main>
        <form action="registerStudent.php" method="POST">
            <label for="fname">First Name:</label>
            <input type="text" id="fname" name="fname" required><br>

            <label for="lname">Last Name:</label>
            <input type="text" id="lname" name="lname" required><br>

            <label for="registrationNo">Registration Number:</label>
            <input type="text" id="registrationNo" name="registrationNo" required><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br>

            <button type="submit">Register</button>
        </form>
    </main>


    <footer>
        <p>&copy; 2024 Student Registration | Developer Yuvraj Sinha</p>
    </footer>

</body>
</html>
