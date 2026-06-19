<?php
// reflected_xss.php
session_start();
require_once 'telemetry.php';

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

$query = $_GET['q'] ?? '';

if ($query !== '') {
    log_security_event("SEARCH_EXECUTED", [
        "username" => $_SESSION['username'],
        "contains_script" => (stripos($query, '<script>') !== false)
    ]);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cloud Notes - Search Directory</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="nav-bar">
        <div>
            <a href="dashboard.php">My Workspace</a>
            <a href="comment.php">Team Board</a>
            <a href="reflected_xss.php" style="background: rgba(255,255,255,0.2);">Search</a>
        </div>
        <a href="logout.php">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a>
    </div>

    <h2>Search Public Directory</h2>
    <p style="margin-bottom: 25px; color: var(--text-muted);">Search across all public team folders and notes.</p>

    <form method="GET" action="reflected_xss.php">
        <input type="text" name="q" placeholder="Enter keyword to search..." value="<?php echo $query; ?>">
        <button type="submit">Search</button>
    </form>

    <?php if ($query !== ''): ?>
        <hr>
        <div class="result" style="padding: 15px; border-left: 4px solid var(--primary); background: #eff6ff; border-radius: 4px;">
            <strong>Search Results for:</strong> <?php echo $query; ?>
            <p style="margin-top: 10px; color: var(--text-muted); background: transparent; border: none; padding: 0; box-shadow: none;"><i>(Search indexing module is currently under maintenance. No documents found.)</i></p>
        </div>
    <?php endif; ?>
</body>
</html>
