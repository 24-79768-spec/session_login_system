<?php
session_start();
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
// Demo account + any accounts registered during this browser session (index2.php)
function all_users() {
    $demo = ['admin@example.com' => ['name' => 'Demo Admin', 'pass' => 'admin123']];
    return $demo + ($_SESSION['users'] ?? []);
}

if (!empty($_SESSION['user'])) { header('Location: home.php'); exit; }
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $users = all_users();
    if (isset($users[$email]) && hash_equals($users[$email]['pass'], $_POST['password'] ?? '')) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['email' => $email, 'name' => $users[$email]['name'], 'login_at' => time()];
        header('Location: home.php');
        exit;
    }
    $error = 'Incorrect email or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Log in</title>
<style>
:root{--ink:#2d3b4a;--mute:#6b7b8a;--blue:#5a7a99;--blue-d:#445f7a;--line:#c6d1da}
*{box-sizing:border-box}
body{margin:0;min-height:100vh;display:grid;place-items:center;padding:24px;color:var(--ink);
font:16px/1.5 "Segoe UI",system-ui,-apple-system,Roboto,sans-serif;
background:radial-gradient(ellipse at center,#f2f2e4 0%,#e8ebe4 45%,#c9d4d8 75%,#8ba2b8 100%) fixed}
.card{width:100%;max-width:400px;padding:34px;border-radius:14px;background:rgba(255,255,255,.72);
border:1px solid rgba(255,255,255,.9);box-shadow:0 12px 32px rgba(69,94,124,.18)}
.wide{max-width:560px}
h1{margin:0 0 4px;font-size:1.65rem;font-weight:700}
.sub{margin:0 0 18px;color:var(--mute)}
label{display:block;margin:14px 0 6px;font-size:.85rem;font-weight:600}
input{width:100%;padding:11px 12px;border:1px solid var(--line);border-radius:8px;background:#fff;font:inherit;color:inherit}
input:focus-visible,button:focus-visible,a:focus-visible{outline:3px solid rgba(90,122,153,.45);outline-offset:1px}
button,.btn{display:block;width:100%;margin-top:20px;padding:12px;border:0;border-radius:8px;background:var(--blue);
color:#fff;font:inherit;font-weight:600;text-align:center;text-decoration:none;cursor:pointer}
button:hover,.btn:hover{background:var(--blue-d)}
.btn.out{background:transparent;color:var(--blue-d);border:1px solid var(--blue)}
.btn.out:hover{background:rgba(90,122,153,.1)}
.alert{padding:10px 12px;margin-bottom:10px;border-radius:8px;font-size:.9rem}
.err{background:#fbe9e6;color:#9c3326}.ok{background:#e3f1e6;color:#2a6640}
.foot{margin-top:18px;text-align:center;font-size:.9rem;color:var(--mute)}
a{color:var(--blue-d)}
.nav{display:flex;gap:18px;margin-bottom:22px;padding-bottom:12px;border-bottom:1px solid var(--line)}
.nav a{font-weight:600;text-decoration:none;color:var(--mute)}.nav a.on{color:var(--ink)}
.nav .sp{margin-left:auto}
dl{margin:18px 0 0;display:grid;grid-template-columns:130px 1fr;gap:10px 12px}
dt{color:var(--mute)}dd{margin:0;font-weight:600;word-break:break-all}
hr{border:0;border-top:1px solid var(--line);margin:24px 0 4px}
@media(prefers-reduced-motion:no-preference){button,.btn{transition:background .15s}}
</style>
</head>
<body>
<div class="card">
  <h1>Welcome back</h1>
  <p class="sub">Log in to continue.</p>
  <?php if ($error): ?><div class="alert err"><?= e($error) ?></div><?php endif; ?>
  <?php if (isset($_GET['registered'])): ?><div class="alert ok">Account created. You can log in now.</div><?php endif; ?>
  <?php if (isset($_GET['out'])): ?><div class="alert ok">You have been logged out.</div><?php endif; ?>

  <form method="post">
    <label for="email">Email</label>
    <input id="email" type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>">
    <label for="password">Password</label>
    <input id="password" type="password" name="password" required>
    <button type="submit">Log in</button>
  </form>
  <p class="foot">No account yet? <a href="index2.php">Sign up</a></p>
  <p class="foot">Demo: admin@example.com / admin123</p>
</div>
</body>
</html>
