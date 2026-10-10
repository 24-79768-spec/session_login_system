<?php
require 'db.php';
$user = require_login($pdo);
$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
        $error = 'Your session expired. Please try again.';
    } elseif (($_POST['action'] ?? '') === 'name') {
        $name = trim($_POST['fullname'] ?? '');
        if ($name === '') {
            $error = 'Name cannot be empty.';
        } else {
            $pdo->prepare('UPDATE users SET fullname = ? WHERE id = ?')->execute([$name, $user['id']]);
            $user['fullname'] = $name;
            $success = 'Name updated.';
        }
    } else {
        $s = $pdo->prepare('SELECT password FROM users WHERE id = ?');
        $s->execute([$user['id']]);
        $hash = $s->fetchColumn();
        if (!password_verify($_POST['current'] ?? '', $hash)) {
            $error = 'Your current password is incorrect.';
        } elseif (strlen($_POST['new'] ?? '') < 6) {
            $error = 'Use a new password with at least 6 characters.';
        } else {
            $pdo->prepare('UPDATE users SET password = ? WHERE id = ?')
                ->execute([password_hash($_POST['new'], PASSWORD_DEFAULT), $user['id']]);
            $success = 'Password changed.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Profile</title>
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
<div class="card wide">
  <div class="nav">
    <a href="home.php" class="">Home</a>
    <a href="home2.php" class="on">Profile</a>
    <a href="home.php?logout=1" class="sp">Log out</a>
  </div>
  <h1>Your profile</h1>
  <p class="sub">Update your name or change your password.</p>

  <?php if ($error): ?><div class="alert err"><?= e($error) ?></div><?php endif; ?>
  <?php if ($success): ?><div class="alert ok"><?= e($success) ?></div><?php endif; ?>

  <form method="post">
    <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
    <input type="hidden" name="action" value="name">
    <label for="fullname">Full name</label>
    <input id="fullname" name="fullname" required value="<?= e($user['fullname']) ?>">
    <button type="submit">Save name</button>
  </form>

  <hr>

  <form method="post">
    <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
    <input type="hidden" name="action" value="password">
    <label for="current">Current password</label>
    <input id="current" type="password" name="current" required>
    <label for="new">New password</label>
    <input id="new" type="password" name="new" required>
    <button type="submit">Change password</button>
  </form>
</div>
</body>
</html>
