<?php
// settings.php
session_start();
include 'con.php';
require_once 'telemetry.php';

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';

// Handle Email Update (🚨 VULNERABLE TO CSRF - No Token Validation)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_email'])) {
    $new_email = $_POST['new_email'];
    
    // Also vulnerable to SQLi, compounding the threat
    $update_query = "UPDATE users SET email = '$new_email' WHERE id = $user_id";
    
    if (mysqli_query($conn, $update_query)) {
        $message = "<div class='alert-box' style='border-left: 4px solid #10b981; background: #ecfdf5; color: #059669;'><strong>Success!</strong> Email updated successfully to: " . htmlspecialchars($new_email) . "</div>";
        
        // --- TELEMETRY: Critical for RL Agent to detect Account Takeover ---
        log_security_event("EMAIL_CHANGED", [
            "username" => $_SESSION['username'], 
            "new_email" => $new_email,
            "ip_origin" => $_SERVER['REMOTE_ADDR']
        ]);
        // -------------------------------------------------------------------
    } else {
        $message = "<div class='alert-box' style='border-left: 4px solid var(--danger); background: #fef2f2; color: var(--danger);'>Database Error: " . mysqli_error($conn) . "</div>";
    }
}

// Fetch current user details to display
$fetch_query = "SELECT email FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $fetch_query);
$current_user_data = mysqli_fetch_assoc($result);
$current_email = $current_user_data['email'];

?>
<!DOCTYPE html>
<html>
<head>
    <title>Cloud Notes - Settings</title>
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
                <a href="comment.php">💬 Submit Feedback</a>
                <a href="reflected_xss.php">🔍 Search Directory</a>
                <a href="settings.php" class="active">⚙️ Account Settings</a>
            </nav>
            <div class="sidebar-footer">
                <a href="logout.php" class="logout-btn">🚪 Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a>
            </div>
        </aside>

        <main class="main-content">
            <div class="content-wrapper">
                
                <h2>Account Settings</h2>
                <p style="margin-bottom: 25px; color: var(--text-muted);">Manage your personal information and workspace preferences.</p>
                
                <?php echo $message; ?>

                <div class="card">
                    <h3>Update Email Address</h3>
                    <p style="color: var(--text-muted); font-size: 0.9rem;">Current Email: <strong><?php echo htmlspecialchars($current_email); ?></strong></p>
                    
                    <form method="POST" action="settings.php" style="box-shadow: none; border: none; padding: 0; margin: 0; background: transparent;">
                        <label>New Email Address:</label>
                        <input type="email" name="new_email" placeholder="Enter new email address" style="width: 100%; padding: 12px 15px; margin: 8px 0 20px 0; border: 1px solid var(--border); border-radius: var(--radius); box-sizing: border-box; font-family: inherit; font-size: 1rem; background-color: #f8fafc;" required>
                        <button type="submit">Update Email</button>
                    </form>
                </div>
                
            </div>
        </main>
    </div>
</body>
</html>
