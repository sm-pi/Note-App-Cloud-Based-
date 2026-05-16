<?php
session_start();

// ==========================================
// MODULE 1: DATABASE & CONFIGURATION
// ==========================================
// SQLite creates a local file named 'vuln_notes.sqlite' in the same directory.
$db = new SQLite3('vuln_notes.sqlite');

// Initialize tables if they don't exist
$db->exec("CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT, 
    username TEXT, 
    password TEXT
)");

$db->exec("CREATE TABLE IF NOT EXISTS notes (
    id INTEGER PRIMARY KEY AUTOINCREMENT, 
    user_id INTEGER, 
    title TEXT, 
    content TEXT
)");

// Insert a dummy user so you have an account to hack/login to
$db->exec("INSERT OR IGNORE INTO users (id, username, password) VALUES (1, 'admin', 'supersecret')");


// ==========================================
// MODULE 2: MODELS (INTENTIONALLY VULNERABLE)
// ==========================================

function loginUser($db, $username, $password) {
    // 🚨 VULNERABILITY: Direct string concatenation. Ripe for Auth Bypass SQLi.
    // Try username: admin' -- 
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $db->query($query);
    return $result->fetchArray(SQLITE3_ASSOC);
}

function getNotes($db, $user_id, $search = '') {
    // 🚨 VULNERABILITY: The $search variable is concatenated directly. Ripe for UNION SQLi.
    $query = "SELECT * FROM notes WHERE user_id = $user_id";
    if (!empty($search)) {
        $query .= " AND title LIKE '%$search%'";
    }
    
    $results = [];
    $res = $db->query($query);
    if ($res) {
        while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
            $results[] = $row;
        }
    }
    return $results;
}

function addNote($db, $user_id, $title, $content) {
    // 🚨 VULNERABILITY: Also vulnerable to SQL injection (INSERT based) and XSS.
    $query = "INSERT INTO notes (user_id, title, content) VALUES ($user_id, '$title', '$content')";
    $db->exec($query);
}


// ==========================================
// MODULE 3: CONTROLLER LOGIC (ROUTING)
// ==========================================

$action = $_GET['action'] ?? 'home';

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'login') {
        $user = loginUser($db, $_POST['username'], $_POST['password']);
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: ?action=dashboard");
            exit;
        } else {
            $error = "Invalid credentials!";
        }
    } elseif ($action === 'add_note' && isset($_SESSION['user_id'])) {
        addNote($db, $_SESSION['user_id'], $_POST['title'], $_POST['content']);
        header("Location: ?action=dashboard");
        exit;
    }
}

if ($action === 'logout') {
    session_destroy();
    header("Location: ?action=home");
    exit;
}


// ==========================================
// MODULE 4: VIEWS (HTML / UI)
// ==========================================
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VulnNotes App</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 font-sans p-8">

    <div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
        
        <div class="flex justify-between items-center border-b pb-4 mb-4">
            <h1 class="text-2xl font-bold">📝 VulnNotes</h1>
            <?php if (isset($_SESSION['user_id'])): ?>
                <div>
                    <span class="mr-4">Hello, <strong><?= $_SESSION['username'] ?></strong></span>
                    <a href="?action=logout" class="text-red-500 hover:underline">Logout</a>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!isset($_SESSION['user_id'])): ?>
            <h2 class="text-xl mb-4">Login</h2>
            <?php if(isset($error)) echo "<p class='text-red-500 mb-4'>$error</p>"; ?>
            
            <form action="?action=login" method="POST" class="flex flex-col gap-4">
                <input type="text" name="username" placeholder="Username" class="border p-2 rounded">
                <input type="password" name="password" placeholder="Password" class="border p-2 rounded">
                <button type="submit" class="bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Login</button>
            </form>
            <p class="mt-4 text-sm text-gray-500">Hint: Try SQL injection on the username field.</p>

        <?php else: ?>
            
            <div class="bg-gray-50 p-4 rounded mb-6 border">
                <h3 class="font-bold mb-2">Add a new note</h3>
                <form action="?action=add_note" method="POST" class="flex flex-col gap-2">
                    <input type="text" name="title" placeholder="Note Title" class="border p-2 rounded">
                    <textarea name="content" placeholder="Note Content" class="border p-2 rounded"></textarea>
                    <button type="submit" class="bg-green-500 text-white p-2 rounded hover:bg-green-600 w-32">Save Note</button>
                </form>
            </div>

            <form action="" method="GET" class="mb-4 flex gap-2">
                <input type="hidden" name="action" value="dashboard">
                <input type="text" name="search" placeholder="Search notes..." class="border p-2 rounded flex-1">
                <button type="submit" class="bg-gray-200 p-2 rounded hover:bg-gray-300">Search</button>
            </form>

            <div>
                <h3 class="font-bold mb-2">Your Notes</h3>
                <?php 
                    $searchQuery = $_GET['search'] ?? '';
                    $notes = getNotes($db, $_SESSION['user_id'], $searchQuery);
                    
                    if (count($notes) === 0) {
                        echo "<p class='text-gray-500'>No notes found.</p>";
                    } else {
                        foreach ($notes as $note) {
                            echo "<div class='border-l-4 border-blue-500 pl-4 py-2 mb-4'>";
                            // Vulnerable to XSS because we aren't using htmlspecialchars()
                            echo "<h4 class='font-bold'>" . $note['title'] . "</h4>";
                            echo "<p>" . $note['content'] . "</p>";
                            echo "</div>";
                        }
                    }
                ?>
            </div>

        <?php endif; ?>

    </div>
</body>
</html>
