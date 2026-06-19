<?php
session_start();
$commentsFile = "comments.txt";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION['username'])) {
    file_put_contents($commentsFile, $_POST['comment'] . "<br>\n", FILE_APPEND);
}
?>
<h2>Comment Section</h2>
<?php if (isset($_SESSION['username'])): ?>
<form method="POST">
    <textarea name="comment" rows="4" cols="40"></textarea><br>
    <input type="submit" value="Submit">
</form>
<?php else: ?>
<p>You must <a href="login.php">login</a> to comment.</p>
<?php endif; ?>
<hr>
<h3>All Comments</h3>
<?php
if (file_exists($commentsFile)) {
    echo file_get_contents($commentsFile);
}
?>
