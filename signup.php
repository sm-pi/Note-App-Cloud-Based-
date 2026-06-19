<?php
// signup.php
session_start();
include 'con.php';
require_once 'telemetry.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // 🚨 VULNERABLE SQL QUERY (Insert Injection)
    $check_query = "SELECT * FROM users WHERE name = '$username' OR email = '$email'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $message = "<div class='alert-box' style='border-left: 4px solid var(--danger); background: #fef2f2; color: var(--danger);'>Username or Email already exists!</div>";
    } else {
        $insert_query = "INSERT INTO users (name, email, password, role_id) VALUES ('$username', '$email', '$password', 1)";
        if (mysqli_query($conn, $insert_query)) {
            log_security_event("USER_REGISTERED", ["username" => $username, "email" => $email]);
            header("Location: index.php?msg=registered");
            exit;
        } else {
            $message = "<div class='alert-box' style='border-left: 4px solid var(--danger); background: #fef2f2; color: var(--danger);'>Database Error: " . mysqli_error($conn) . "</div>";
            log_security_event("SQL_ERROR", ["query" => $insert_query, "error" => mysqli_error($conn)]);
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cloud Notes - Sign Up</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: var(--primary);">☁️ Cloud Notes</h1>
            <p style="color: var(--text-muted);">Create your workspace account.</p>
        </div>

        <h2>Sign Up</h2>
        <?php echo $message; ?>
        
        <form method="POST" action="signup.php">
            <label>Username:</label><br>
            <input type="text" name="username" placeholder="Choose a username" required><br>

            <label>Email Address:</label><br>
            <input type="email" name="email" placeholder="name@company.com" style="width: 100%; padding: 12px 15px; margin: 8px 0 20px 0; border: 1px solid var(--border); border-radius: var(--radius); box-sizing: border-box; font-family: inherit; font-size: 1rem; background-color: #f8fafc;" required><br>
            
            <label>Password:</label><br>
            <input type="password" name="password" placeholder="Create a password" required><br>
            
            <button type="submit" style="margin-top: 15px;">Create Account</button>
        </form>
        
        <div style="text-align: center; margin-top: 20px;">
            <p style="box-shadow: none; border: none; background: transparent;">Already have an account? <a href="index.php" style="color: var(--primary); font-weight: bold; text-decoration: none;">Login Here</a></p>
        </div>
    </div>
</body>
</html>
