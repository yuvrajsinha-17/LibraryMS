<?php
session_start();
include 'db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $registrationNo = $_POST['registrationNo'];
    $password = $_POST['password'];


    $registrationNo = mysqli_real_escape_string($mysqli, $registrationNo);


    $query = "SELECT * FROM students WHERE registration_number = '$registrationNo'";
    $result = mysqli_query($mysqli, $query);

    if (mysqli_num_rows($result) > 0) {
  
        $students = mysqli_fetch_assoc($result);
        

        if (password_verify($password, $students['password'])) {

            $_SESSION['student_id'] = $students['id']; 
            $_SESSION['registrationNo'] = $students['registration_number']; 
            

            header("Location: students/studentDashboard.php");
            exit();
        } else {
            $error_message = "Invalid password.";
        }
    } else {
        $error_message = "Student not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>
</head>
<body>

    <div class="container">
        <div class="login-page">
            <h2>Login</h2>
            <form action="studentLogin.php" method="POST">
                <label for="registrationNo">Registration No:</label>
                <input type="text" id="registrationNo" name="registrationNo" required><br><br>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required><br><br>
                <button type="submit">Login</button>
            </form>
            <p class="register-message">If you have no membership, please click to <a href="registerStudent.php">Register</a></p>
            <?php if (isset($error_message)) { echo "<p style='color:red;'>$error_message</p>"; } ?>
        </div>
    </div>
</body>
</html>
