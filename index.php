<?php
// index.php
session_start();
include 'con.php';
require_once 'telemetry.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_id = $_POST['login_id']; // Can be username OR email
    $password = $_POST['password'];

    // 🚨 VULNERABLE SQL QUERY (Auth Bypass - Now supports Email or Username)
    $query = "SELECT * FROM users WHERE (name = '$login_id' OR email = '$login_id') AND password = '$password'";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        $error = "Database Error: " . mysqli_error($conn);
        log_security_event("SQL_ERROR", ["query" => $query, "error" => mysqli_error($conn)]);
    } else if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['name']; 
        
        // Session Binding for Hijack Detection
        $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        log_security_event("LOGIN_SUCCESS", ["username" => $user['name']]);
        
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid credentials!";
        log_security_event("LOGIN_FAILED", ["attempted_id" => $login_id]);
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
    <div class="login-container">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: var(--primary);">☁️ Cloud Notes</h1>
            <p style="color: var(--text-muted);">Secure team collaboration and note management.</p>
        </div>

        <h2>Workspace Login</h2>
        <?php if ($error) echo "<div class='alert-box' style='border-left: 4px solid var(--danger); background: #fef2f2; color: var(--danger);'>$error</div>"; ?>
        
        <form method="POST" action="index.php">
            <label>Username or Email:</label><br>
            <input type="text" name="login_id" placeholder="Enter username or email" required><br>
            
            <label>Password:</label><br>
            <input type="password" name="password" id="pwd" placeholder="Enter your password" required><br>
            
            <div style="display: flex; align-items: center; margin-top: 5px;">
                <input type="checkbox" onclick="togglePassword()" id="showPwd" style="margin: 0 10px 0 0; width: auto;">
                <label for="showPwd" style="font-weight: normal; cursor: pointer; margin: 0;">Show Password</label>
            </div>
            
            <button type="submit" style="margin-top: 15px;">Login</button>
        </form>
        
        <div style="text-align: center; margin-top: 20px;">
            <p style="box-shadow: none; border: none; background: transparent;">Don't have an account? <a href="signup.php" style="color: var(--primary); font-weight: bold; text-decoration: none;">Sign Up Here</a></p>
        </div>
    </div>

    <script>
        function togglePassword() {
            var passwordField = document.getElementById("pwd");
            if (passwordField.type === "password") {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        }
    </script>
</body>
</html>
