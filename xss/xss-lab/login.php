<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    if (($user === 'victim' && $pass === '1234') || ($user === 'attacker' && $pass==='1234')) {
        $_SESSION['username'] = $user;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid credentials";
    }
}
?>
<h2>Login</h2>
<form method="POST">
    Username: <input type="text" name="username"><br>
    Password: <input type="password" name="password"><br>
    <input type="submit" value="Login">
</form>
<?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
