<?php
// dashboard.php
session_start();
include 'con.php';

// Kick the user out if they aren't logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$name = $_SESSION['name'];

// Handle new note submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    
    // 🚨 VULNERABLE INSERT QUERY
    $insert_query = "INSERT INTO notes (user_id, title, content) VALUES ($user_id, '$title', '$content')";
    mysqli_query($conn, $insert_query);
}

// Fetch user's notes
$notes_query = "SELECT * FROM notes WHERE user_id = $user_id";
$notes_result = mysqli_query($conn, $notes_query);
?>

<!DOCTYPE html>
<html>
   <head>
    <title>Dashboard - Cloud Notes</title>
    <link rel="stylesheet" href="style.css">

</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($name); ?>! | <a href="logout.php">Logout</a></h2>
    
    <hr>
    
    <h3>Your Notes</h3>
    <ul>
        <?php while ($note = mysqli_fetch_assoc($notes_result)): ?>
            <li>
                <strong><?php echo htmlspecialchars($note['title']); ?></strong><br>
                <?php echo htmlspecialchars($note['content']); ?>
            </li><br>
        <?php endwhile; ?>
    </ul>
    
    <hr>
    
    <h3>Add a New Note</h3>
    <form method="POST" action="dashboard.php">
        <label>Title:</label><br>
        <input type="text" name="title"><br><br>
        
        <label>Content:</label><br>
        <textarea name="content" rows="4" cols="30"></textarea><br><br>
        
        <button type="submit">Add Note</button>
    </form>
</body>
</html>
