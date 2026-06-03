<?php
// index.php
session_start();
include 'con.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 🚨 VULNERABLE SQL QUERY (For testing SQLi)
    $query = "SELECT * FROM users WHERE name = '$username' AND password = '$password'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cloud Notes - Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Login to Cloud Notes</h2>
    <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
    
    <form method="POST" action="index.php">
        <label>Username:</label><br>
        <input type="text" name="username"><br><br>
        
        <label>Password:</label><br>
        <input type="password" name="password" id="pwd" style="margin-bottom: 5px;"><br>
        
        <div style="display: flex; align-items: center; margin-bottom: 20px;">
            <input type="checkbox" onclick="togglePassword()" id="showPwd" style="margin: 0 10px 0 0; width: auto;">
            <label for="showPwd" style="font-weight: normal; cursor: pointer; margin: 0;">Show Password</label>
        </div>
        
        <button type="submit">Login</button>
    </form>
    <script>
        function togglePassword() {
            // Find the password input box
            var passwordField = document.getElementById("pwd");
            
            // If it's currently hidden, change it to text
            if (passwordField.type === "password") {
                passwordField.type = "text";
            } 
            // Otherwise, change it back to hidden
            else {
                passwordField.type = "password";
            }
        }
    </script>
</body>
</html>
