<?php
// login.php
session_start();
include 'con.php';
require_once 'telemetry.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Hardcoded lab credentials
    if (($user === 'victim' && $pass === '1234') || ($user === 'attacker' && $pass==='1234')) {
        $_SESSION['username'] = $user;

        // FIX: populate user_id from DB so dashboard.php / settings.php
        // don't build queries with an empty id (WHERE id = ) and error out.
        $lookup = mysqli_query($conn, "SELECT id FROM users WHERE name = '$user' LIMIT 1");
        if ($lookup && ($row = mysqli_fetch_assoc($lookup))) {
            $_SESSION['user_id'] = $row['id'];
        }
        
        // --- TELEMETRY & SESSION BINDING ---
        $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        
        log_security_event("LOGIN_SUCCESS", ["username" => $user]);
        // -----------------------------------

        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid credentials";
        log_security_event("LOGIN_FAILED", ["attempted_username" => $user]);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Lab Login</title>
</head>
<body>
    <h2>Login</h2>
    <form method="POST">
        Username: <input type="text" name="username"><br>
        Password: <input type="password" name="password"><br>
        <input type="submit" value="Login">
    </form>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
</body>
</html>
