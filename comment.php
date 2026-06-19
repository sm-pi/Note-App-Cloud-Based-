<?php
// comment.php
session_start();
require_once 'telemetry.php';

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

// We will keep writing to the same file to preserve your existing lab structure
$commentsFile = "comments.txt";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Variable updated to reflect the new form input name
    $raw_feedback = $_POST['feedback'];
    
    // 🚨 VULNERABLE FILE WRITE (Stored XSS)
    $formatted_feedback = "<strong>" . htmlspecialchars($_SESSION['username']) . ":</strong> " . $raw_feedback . "<br>\n";
    file_put_contents($commentsFile, $formatted_feedback, FILE_APPEND);
    
    // Telemetry event updated for your RL agent tracking
    log_security_event("FEEDBACK_SUBMITTED", [
        "username" => $_SESSION['username'],
        "contains_script_tags" => (stripos($raw_feedback, '<script>') !== false)
    ]);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cloud Notes - Submit Feedback</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="app-layout">
        
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>☁️ Cloud Notes</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php">📊 My Workspace</a>
                <a href="comment.php" class="active">💬 Submit Feedback</a>
                <a href="reflected_xss.php">🔍 Search Directory</a>
            </nav>
            <div class="sidebar-footer">
                <a href="logout.php" class="logout-btn">🚪 Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a>
            </div>
        </aside>

        <main class="main-content">
            <div class="content-wrapper">
                
                <h2>Application Feedback</h2>
                <p style="margin-bottom: 25px; color: var(--text-muted);">Encountered a bug or have a feature request? Submit your feedback below so our admin team can review it. (Supports rich text framing).</p>
                
                <div class="card">
                    <form method="POST" action="comment.php" style="box-shadow: none; border: none; padding: 0; margin: 0; background: transparent;">
                        <label>Feedback Details:</label><br>
                        <textarea name="feedback" rows="4" placeholder="Describe the issue or feature request..."></textarea>
                        <button type="submit" style="margin-top: 10px;">Submit Feedback</button>
                    </form>
                </div>
                
                <hr>
                <h3>Recent Feedback Reports</h3>
                <div class="comments-container" style="background: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid var(--border);">
                    <?php
                    if (file_exists($commentsFile)) {
                        echo file_get_contents($commentsFile);
                    } else {
                        echo "<span style='color: var(--text-muted);'>No feedback submitted yet.</span>";
                    }
                    ?>
                </div>
                
            </div>
        </main>
    </div>
</body>
</html>
