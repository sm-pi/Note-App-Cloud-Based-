<?php
// dashboard.php
session_start();
include 'con.php';
require_once 'telemetry.php';

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

// Session Hijack Detection
$current_ip = $_SERVER['REMOTE_ADDR'];
$current_ua = $_SERVER['HTTP_USER_AGENT'];
if ($_SESSION['ip_address'] !== $current_ip || $_SESSION['user_agent'] !== $current_ua) {
    log_security_event("SESSION_HIJACK_DETECTED", [
        "username" => $_SESSION['username'],
        "original_ip" => $_SESSION['ip_address'],
        "hijacker_ip" => $current_ip
    ]);
}

$user_id = $_SESSION['user_id'];
$message = '';

// Handle New Note Creation (VULNERABLE TO SQL INJECTION)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_note') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    
    $insert_query = "INSERT INTO notes (user_id, title, content) VALUES ($user_id, '$title', '$content')";
    
    if (mysqli_query($conn, $insert_query)) {
        $message = "<div class='alert-box' style='border-left: 4px solid #10b981; background: #ecfdf5;'><strong style='color: #059669;'>Success!</strong> Note saved successfully!</div>";
        log_security_event("NOTE_CREATED", ["username" => $_SESSION['username'], "title" => $title]);
    } else {
        $message = "<div class='alert-box' style='border-left: 4px solid var(--danger); background: #fef2f2;'><strong style='color: var(--danger);'>Database Error:</strong> " . mysqli_error($conn) . "</div>";
        log_security_event("SQL_ERROR", ["query" => $insert_query, "error" => mysqli_error($conn)]);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cloud Notes - My Workspace</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="app-layout">
        
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>☁️ Cloud Notes</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="active">📊 My Workspace</a>
                <a href="comment.php">💬 Submit Feedback</a>
                <a href="reflected_xss.php">🔍 Search Directory</a>
                <a href="settings.php">⚙️ Account Settings</a>
            </nav>
            <div class="sidebar-footer">
                <a href="logout.php" class="logout-btn">🚪 Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a>
            </div>
        </aside>

        <main class="main-content">
            <div class="content-wrapper">
                
                <h2>My Workspace</h2>
                <?php echo $message; ?>

                <div class="card">
                    <h3>➕ Create New Note</h3>
                    <form method="POST" action="dashboard.php" style="box-shadow: none; border: none; padding: 0; margin: 0; background: transparent;">
                        <input type="hidden" name="action" value="create_note">
                        <label>Title:</label>
                        <input type="text" name="title" required placeholder="Project Alpha Notes">
                        <label>Content:</label>
                        <textarea name="content" rows="4" required placeholder="Write your content here..."></textarea>
                        <button type="submit">Save Note</button>
                    </form>
                </div>

                <h3>Saved Notes</h3>
                <?php
                $fetch_query = "SELECT * FROM notes WHERE user_id = $user_id ORDER BY id DESC";
                $notes_result = mysqli_query($conn, $fetch_query);
                
                if (mysqli_num_rows($notes_result) > 0) {
                    echo "<ul>";
                    while ($note = mysqli_fetch_assoc($notes_result)) {
                        echo "<li>";
                        echo "<strong>" . htmlspecialchars($note['title']) . "</strong><br>";
                        echo "<span style='color: var(--text-muted);'>" . htmlspecialchars($note['content']) . "</span>";
                        echo "</li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<p class='card' style='text-align: center; color: var(--text-muted);'>You have no saved notes.</p>";
                }
                ?>
                
            </div>
        </main>
    </div>
</body>
</html>
