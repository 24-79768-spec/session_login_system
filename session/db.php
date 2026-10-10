<?php
// Database connection + shared helpers. Edit the 4 values below to match your MySQL setup.
$DB_HOST = 'localhost';
$DB_NAME = 'login_system';
$DB_USER = 'root';
$DB_PASS = '';

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('Database connection failed. Did you import login_system.sql and check db.php?');
}

session_start();
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}

// Any page can log out with ?logout=1
if (isset($_GET['logout'])) {
    $_SESSION = [];
    session_destroy();
    header('Location: index.php');
    exit;
}

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// Call at the top of protected pages; returns the logged-in user row.
function require_login(PDO $pdo) {
    if (empty($_SESSION['user_id'])) {
        header('Location: index.php');
        exit;
    }
    $s = $pdo->prepare('SELECT id, fullname, email, created_at FROM users WHERE id = ?');
    $s->execute([$_SESSION['user_id']]);
    $u = $s->fetch();
    if (!$u) { header('Location: index.php?logout=1'); exit; }
    return $u;
}
